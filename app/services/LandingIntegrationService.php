<?php

class LandingIntegrationService {
    private bool $ready = false;
    private const WHATSAPP_POPUP_MESSAGE = 'El siguiente enlace te lleva a WhatsApp con un mensaje ya escrito para el chat de reservaciones. Envía tal cual el mensaje inicial para comenzar con tu reserva de cita.';

    public function getStatus(array $user): array {
        $slug = $this->defaultSlug($user);

        try {
            $this->boot();
            $landing = $this->findLandingForUser($user);
            if ($landing) $slug = $landing['slug'];

            return [
                'available' => true,
                'landing' => $landing,
                'slug' => $slug,
                'public_url' => $landing && !empty($landing['activo']) ? $this->publicUrl($slug) : '',
                'whatsapp_url' => $this->landingWhatsAppUrl($landing, $user),
                'logo_url' => $landing ? $this->logoUrl($landing) : '',
                'message' => '',
            ];
        } catch (Throwable $e) {
            error_log('AIDE landing integration: ' . $e->getMessage());

            return [
                'available' => false,
                'landing' => null,
                'slug' => $slug,
                'public_url' => '',
                'whatsapp_url' => $this->defaultWhatsAppUrl($user),
                'logo_url' => '',
                'message' => 'La conexion con el gestor de landings aun no esta disponible.',
            ];
        }
    }

    public function save(
        array $user,
        array $specialist,
        array $form,
        array $services,
        array $branches,
        array $schedules,
        ?array $logoFile = null
    ): array {
        $this->boot(true);

        $landing = $this->findLandingForUser($user);
        $oldSlug = $landing['slug'] ?? '';
        $slug = $this->normalizeSlug($form['slug'] ?? $this->defaultSlug($user));
        $this->validateSlug($slug);

        if (slugExists($slug, $landing ? (int)$landing['id'] : null)) {
            throw new InvalidArgumentException('Ese nombre de carpeta ya pertenece a otra landing.');
        }

        $folderRenamed = false;
        if ($landing && $oldSlug !== $slug) {
            $rename = renombrarLandingPublicada($oldSlug, $slug);
            if (!$rename['ok']) throw new InvalidArgumentException($rename['msg']);
            $folderRenamed = (bool)$rename['renamed'];
        } elseif (!$landing && carpetaLandingPublicadaExiste($slug)) {
            throw new InvalidArgumentException('Ya existe una carpeta publica con ese nombre. Elige otro.');
        }

        $pdo = db();
        $committed = false;

        try {
            $pdo->beginTransaction();
            $defaults = [
                'slug' => $slug,
                'nombre' => trim($form['nombre']),
                'titulo_pagina' => trim($form['titulo_pagina']),
                'color_primario' => $form['color_primario'],
                'color_hover' => $this->darkenHex($form['color_primario']),
                'color_fondo' => $form['color_fondo'],
                'color_dropdown' => $form['color_primario'],
                'logo_path' => '',
                'logo_es_url' => 0,
                'mostrar_logo' => 0,
                'mostrar_redes' => 1,
                'texto_encabezado' => trim($form['texto_encabezado']),
                'activo' => 1,
                'color_social_bg' => $form['color_primario'],
                'extras_html' => '<!-- AIDE_MANAGED:' . (int)$user['id'] . ' -->',
                'color_amenities' => '#374151',
                'color_social_text' => '#4b5563',
                'btn_height' => 68,
                'extras_css' => '',
                'logo_size' => 190,
                'logo_circular' => 1,
                'color_footer_text' => '#4b5563',
            ];

            if ($landing) {
                $data = array_merge($defaults, $landing);
                $data = array_merge($data, [
                    'slug' => $defaults['slug'],
                    'nombre' => $defaults['nombre'],
                    'titulo_pagina' => $defaults['titulo_pagina'],
                    'color_primario' => $defaults['color_primario'],
                    'color_hover' => $defaults['color_hover'],
                    'color_fondo' => $defaults['color_fondo'],
                    'color_dropdown' => $defaults['color_dropdown'],
                    'texto_encabezado' => $defaults['texto_encabezado'],
                    'color_social_bg' => $defaults['color_social_bg'],
                    'extras_html' => $defaults['extras_html'],
                    'activo' => 1,
                ]);
                $landingId = saveLanding($data, (int)$landing['id']);
            } else {
                $data = $defaults;
                $landingId = saveLanding($data);
            }

            $logoUrl = trim($form['logo_url'] ?? '');
            if ($logoUrl !== '') {
                $data['logo_path'] = $logoUrl;
                $data['logo_es_url'] = 1;
                $data['mostrar_logo'] = 1;
                saveLanding($data, $landingId);
            }

            if ($logoFile && ($logoFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $upload = uploadImagen($logoFile, $landingId, 'logo');
                if (!$upload['ok']) {
                    throw new RuntimeException($upload['msg'] ?? 'No se pudo subir el logo.');
                }
                $data['logo_path'] = $upload['filename'];
                $data['logo_es_url'] = 0;
                $data['mostrar_logo'] = 1;
                saveLanding($data, $landingId);
            }

            $this->syncSocialNetworks($landingId, $form);
            $this->syncButtons($landingId, $user, $services, $branches, $schedules, $form);

            $pdo->commit();
            $committed = true;
            $publish = publicarLanding($landingId);

            return [
                'ok' => (bool)$publish['ok'],
                'landing_id' => $landingId,
                'public_url' => $this->publicUrl($slug),
                'message' => $publish['ok']
                    ? 'Landing guardada y publicada correctamente.'
                    : 'La configuracion se guardo, pero no se pudo publicar: ' . $publish['msg'],
            ];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            if (!$committed && $folderRenamed) {
                $restore = renombrarLandingPublicada($slug, $oldSlug);
                if (!$restore['ok']) {
                    error_log('AIDE landing folder rollback: ' . $restore['msg']);
                }
            }
            throw $e;
        }
    }

    public function delete(array $user): array {
        $this->boot(true);

        $landing = $this->findLandingForUser($user);
        if (!$landing) {
            return ['ok' => true, 'message' => 'La landing ya no existe.'];
        }
        $slug = $landing['slug'];

        $publishedDelete = eliminarLandingPublicada($slug);
        if (!$publishedDelete['ok']) {
            throw new RuntimeException($publishedDelete['msg']);
        }

        $images = getImagenesByLanding((int)$landing['id']);
        foreach ($images as $image) {
            $filename = basename((string)($image['nombre_archivo'] ?? ''));
            if ($filename === '') continue;

            $path = rtrim(UPLOADS_DIR, '/\\') . DIRECTORY_SEPARATOR . $filename;
            if (is_file($path) && !unlink($path)) {
                throw new RuntimeException('No se pudo eliminar una imagen asociada a la landing.');
            }
        }

        deleteLanding((int)$landing['id']);

        return [
            'ok' => true,
            'message' => 'Landing eliminada junto con su carpeta publicada y sus archivos.',
        ];
    }

    private function boot(bool $withPublisher = false): void {
        if (!$this->ready) {
            require_once $this->systemPath() . '/db.php';
            db()->query('SELECT 1');
            $this->ready = true;
        }

        if ($withPublisher) {
            require_once $this->systemPath() . '/publisher.php';
        }
    }

    private function systemPath(): string {
        $candidates = [];
        if (defined('LANDINGS_SYSTEM_PATH')) {
            $candidates[] = LANDINGS_SYSTEM_PATH;
        }
        $candidates[] = dirname(ROOT_PATH) . '/SistemaLandings';
        $candidates[] = ROOT_PATH . '/SistemaLandings';

        foreach (array_unique($candidates) as $path) {
            if (is_file($path . '/db.php') && is_file($path . '/publisher.php')) {
                return $path;
            }
        }

        throw new RuntimeException('No se encontro la instalacion de SistemaLandings.');
    }

    private function findLandingBySlug(string $slug): ?array {
        $statement = db()->prepare('SELECT * FROM landings WHERE slug = ? LIMIT 1');
        $statement->execute([$slug]);

        return $statement->fetch() ?: null;
    }

    private function findLandingForUser(array $user): ?array {
        $marker = '<!-- AIDE_MANAGED:' . (int)$user['id'] . ' -->';
        $statement = db()->prepare('SELECT * FROM landings WHERE extras_html LIKE ? ORDER BY id ASC LIMIT 1');
        $statement->execute(['%' . $marker . '%']);
        $landing = $statement->fetch();

        return $landing ?: $this->findLandingBySlug($this->defaultSlug($user));
    }

    private function defaultSlug(array $user): string {
        $name = trim(($user['nombre'] ?? '') . '-' . ($user['apellidos'] ?? ''));
        $name = substr($this->normalizeSlug($name) ?: 'profesional', 0, 55);

        return 'reserva-' . $name . '-' . (int)$user['id'];
    }

    private function normalizeSlug(string $value): string {
        $value = strtr($value, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u', 'Ü' => 'u', 'Ñ' => 'n',
        ]);
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if ($transliterated !== false) $value = $transliterated;
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);

        return substr(trim($value, '-'), 0, 100);
    }

    private function validateSlug(string $slug): void {
        if ($slug === '' || strlen($slug) < 2) {
            throw new InvalidArgumentException('El nombre de la carpeta debe tener al menos 2 caracteres validos.');
        }

        $reserved = ['app', 'chatbot', 'cgi-bin', 'config', 'public', 'sistemalandings', 'ssl'];
        if (in_array(strtolower($slug), $reserved, true)) {
            throw new InvalidArgumentException('Ese nombre de carpeta esta reservado por el servidor. Elige otro.');
        }
    }

    private function publicUrl(string $slug): string {
        $parts = parse_url(BASE_URL);
        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'] ?? ($_SERVER['HTTP_HOST'] ?? 'localhost');
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';

        return $scheme . '://' . $host . $port . '/' . rawurlencode($slug) . '/';
    }

    private function logoUrl(array $landing): string {
        if (empty($landing['logo_path'])) {
            return '';
        }

        return !empty($landing['logo_es_url'])
            ? $landing['logo_path']
            : UPLOADS_URL . ltrim($landing['logo_path'], '/');
    }

    private function landingWhatsAppUrl(?array $landing, array $user): string {
        if ($landing) {
            foreach (getBotones((int)$landing['id']) as $button) {
                if (($button['tipo'] ?? '') === 'whatsapp' && !empty($button['url'])) {
                    return $button['url'];
                }
            }
        }

        return $this->defaultWhatsAppUrl($user);
    }

    private function defaultWhatsAppUrl(array $user): string {
        $fullName = trim(($user['nombre'] ?? '') . ' ' . ($user['apellidos'] ?? ''));
        return getWhatsAppUrl('Hola quiero reservar con ' . $fullName);
    }

    private function syncSocialNetworks(int $landingId, array $form): void {
        $pdo = db();
        $pdo->prepare("DELETE FROM redes_sociales WHERE landing_id = ? AND tipo IN ('instagram', 'facebook')")
            ->execute([$landingId]);

        foreach (['instagram', 'facebook'] as $type) {
            $url = trim($form[$type . '_url'] ?? '');
            if ($url !== '') {
                saveRedSocial([
                    'landing_id' => $landingId,
                    'tipo' => $type,
                    'url' => $url,
                    'orden' => $type === 'instagram' ? 1 : 2,
                ]);
            }
        }
    }

    private function syncButtons(
        int $landingId,
        array $user,
        array $services,
        array $branches,
        array $schedules,
        array $form
    ): void {
        db()->prepare('DELETE FROM botones WHERE landing_id = ?')->execute([$landingId]);

        saveBoton([
            'landing_id' => $landingId,
            'tipo' => 'whatsapp',
            'texto' => 'Reservar cita',
            'url' => $this->defaultWhatsAppUrl($user),
            'activo' => 1,
            'popup_activo' => 1,
            'popup_msg' => self::WHATSAPP_POPUP_MESSAGE,
            'popup_img_activo' => 1,
        ]);

        if ($services) {
            $buttonId = saveBoton([
                'landing_id' => $landingId,
                'tipo' => 'dropdown',
                'texto' => 'Servicios',
                'url' => null,
                'activo' => 1,
            ]);
            foreach ($branches as $branch) {
                $branchServices = array_values(array_filter($services, static function ($service) use ($branch) {
                    return (int)($service['sucursal_id'] ?? 0) === (int)$branch['id'];
                }));
                if (!$branchServices) continue;

                saveDropdownItem([
                    'boton_id' => $buttonId,
                    'tipo' => 'separador',
                    'texto' => $branch['nombre'],
                    'valor' => null,
                    'url' => null,
                ]);

                foreach ($branchServices as $service) {
                    $price = (float)($service['precio'] ?? 0);
                    $duration = (int)($service['duracion'] ?? 0);
                    $details = [];
                    if ($price > 0) $details[] = '$' . number_format($price, 2);
                    if ($duration > 0) $details[] = $duration . ' min';
                    saveDropdownItem([
                        'boton_id' => $buttonId,
                        'tipo' => 'servicio',
                        'texto' => $service['nombre'],
                        'valor' => implode(' / ', $details),
                        'url' => null,
                    ]);
                }
            }
        }

        if ($schedules) {
            $buttonId = saveBoton([
                'landing_id' => $landingId,
                'tipo' => 'dropdown',
                'texto' => 'Horarios',
                'url' => null,
                'activo' => 1,
            ]);
            foreach ($branches as $branch) {
                $branchSchedules = array_values(array_filter($schedules, static function ($schedule) use ($branch) {
                    return (int)($schedule['sucursal_id'] ?? 0) === (int)$branch['id'];
                }));
                if (!$branchSchedules) continue;

                saveDropdownItem([
                    'boton_id' => $buttonId,
                    'tipo' => 'separador',
                    'texto' => $branch['nombre'],
                    'valor' => null,
                    'url' => null,
                ]);

                foreach ($branchSchedules as $schedule) {
                    saveDropdownItem([
                        'boton_id' => $buttonId,
                        'tipo' => 'horario',
                        'texto' => $schedule['etiqueta'],
                        'valor' => $schedule['horario'],
                        'url' => null,
                    ]);
                }
            }
        }

        if ($branches) {
            $buttonId = saveBoton([
                'landing_id' => $landingId,
                'tipo' => 'dropdown',
                'texto' => count($branches) > 1 ? 'Ubicaciones' : 'Ubicacion',
                'url' => null,
                'activo' => 1,
            ]);
            foreach ($branches as $branch) {
                $address = trim(implode(', ', array_filter([
                    $branch['direccion'] ?? '',
                    $branch['ciudad'] ?? '',
                    $branch['estado'] ?? '',
                ])));
                $locationUrl = trim((string)($form['ubicacion_urls'][(int)$branch['id']] ?? ''));
                if ($locationUrl === '' && $address !== '') {
                    $locationUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($address);
                }

                saveDropdownItem([
                    'boton_id' => $buttonId,
                    'tipo' => 'link',
                    'texto' => $branch['nombre'],
                    'valor' => 'AIDE_BRANCH:' . (int)$branch['id'],
                    'url' => $locationUrl ?: '#',
                ]);
            }
        }
    }

    private function darkenHex(string $hex): string {
        $hex = ltrim($hex, '#');
        if (!preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            return '#155e75';
        }

        $parts = str_split($hex, 2);
        $parts = array_map(static fn($part) => max(0, (int)round(hexdec($part) * 0.78)), $parts);

        return sprintf('#%02x%02x%02x', $parts[0], $parts[1], $parts[2]);
    }
}
