<?php

require_once __DIR__ . '/BaseController.php';
require_once APP_PATH . '/services/LandingIntegrationService.php';

class LandingController extends BaseController {
    public function index() {
        $this->requireRole(ROLE_SPECIALIST);

        $user = currentUser();
        $specialist = $this->db->fetch(
            'SELECT * FROM especialistas WHERE usuario_id = ? AND activo = 1 AND autorizado = 1 ORDER BY id LIMIT 1',
            [$user['id']]
        );

        if (!$specialist) {
            setFlashMessage('error', 'No se encontro el perfil de especialista.');
            redirect('/dashboard');
        }

        $services = $this->getServices((int)$user['id']);
        $branches = $this->getBranches((int)$user['id']);
        $schedules = $this->getSchedules((int)$user['id']);
        $integration = new LandingIntegrationService();
        $landingStatus = $integration->getStatus($user);
        $locationUrls = $this->getLandingLocationUrls($landingStatus, $branches);

        if ($this->isPost()) {
            if (!$this->validateCsrf($_POST['csrf'] ?? '')) {
                setFlashMessage('error', 'La sesion del formulario vencio. Intenta nuevamente.');
                redirect('/mi-landing');
            }

            try {
                $form = $this->validatedForm(
                    $user,
                    $specialist,
                    $branches,
                    $locationUrls,
                    $landingStatus['slug'] ?? ''
                );
                $result = $integration->save(
                    $user,
                    $specialist,
                    $form,
                    $services,
                    $branches,
                    $schedules,
                    $_FILES['logo_file'] ?? null
                );

                setFlashMessage($result['ok'] ? 'success' : 'error', $result['message']);
            } catch (InvalidArgumentException $e) {
                setFlashMessage('error', $e->getMessage());
            } catch (Throwable $e) {
                error_log('AIDE landing save: ' . $e->getMessage());
                setFlashMessage('error', 'No fue posible guardar la landing. Verifica la conexion con SistemaLandings e intenta nuevamente.');
            }

            redirect('/mi-landing');
        }

        if (empty($_SESSION['landing_csrf'])) {
            $_SESSION['landing_csrf'] = bin2hex(random_bytes(32));
        }

        $landing = $landingStatus['landing'];
        $socials = $this->getLandingSocials($landingStatus);
        $defaults = [
            'slug' => $landingStatus['slug'] ?? '',
            'nombre' => trim($user['nombre'] . ' ' . $user['apellidos']),
            'titulo_pagina' => 'Reserva con ' . trim($user['nombre'] . ' ' . $user['apellidos']),
            'texto_encabezado' => trim(($specialist['profesion'] ?? '') . ' ' . ($specialist['especialidad'] ?? '')),
            'color_primario' => '#0f766e',
            'color_fondo' => '#f0fdfa',
            'logo_url' => '',
            'instagram_url' => $socials['instagram'],
            'facebook_url' => $socials['facebook'],
            'ubicacion_urls' => $locationUrls,
        ];

        if ($landing) {
            $defaults = array_merge($defaults, [
                'slug' => $landing['slug'] ?? $defaults['slug'],
                'nombre' => $landing['nombre'] ?? $defaults['nombre'],
                'titulo_pagina' => $landing['titulo_pagina'] ?? $defaults['titulo_pagina'],
                'texto_encabezado' => $landing['texto_encabezado'] ?? $defaults['texto_encabezado'],
                'color_primario' => $landing['color_primario'] ?? $defaults['color_primario'],
                'color_fondo' => $this->normalizeBackground($landing['color_fondo'] ?? $defaults['color_fondo']),
                'logo_url' => !empty($landing['logo_es_url']) ? ($landing['logo_path'] ?? '') : '',
            ]);
        }

        $this->render('landings/index', [
            'title' => 'Mi landing',
            'user' => $user,
            'specialist' => $specialist,
            'landingStatus' => $landingStatus,
            'form' => $defaults,
            'services' => $services,
            'branches' => $branches,
            'schedules' => $schedules,
            'branchContent' => $this->groupContentByBranch($branches, $services, $schedules, $locationUrls),
            'csrf' => $_SESSION['landing_csrf'],
        ]);
    }

    public function delete() {
        $this->requireRole(ROLE_SPECIALIST);

        if (!$this->isPost()) {
            redirect('/mi-landing');
        }

        if (!$this->validateCsrf($_POST['csrf'] ?? '')) {
            setFlashMessage('error', 'La sesion del formulario vencio. Intenta nuevamente.');
            redirect('/mi-landing');
        }

        try {
            $result = (new LandingIntegrationService())->delete(currentUser());
            setFlashMessage($result['ok'] ? 'success' : 'error', $result['message']);
        } catch (Throwable $e) {
            error_log('AIDE landing delete: ' . $e->getMessage());
            setFlashMessage('error', 'No fue posible eliminar completamente la landing. Intenta nuevamente.');
        }

        redirect('/mi-landing');
    }

    private function getServices(int $userId): array {
        return $this->db->fetchAll(
            "SELECT DISTINCT e.sucursal_id,
                    suc.nombre AS sucursal,
                    s.nombre,
                    COALESCE(es.precio_personalizado, s.precio, 0) AS precio,
                    COALESCE(es.duracion_personalizada, s.duracion_minutos, 0) AS duracion
             FROM especialistas e
             JOIN sucursales suc ON suc.id = e.sucursal_id AND suc.activo = 1 AND suc.autorizado = 1
             JOIN especialistas_servicios es ON es.especialista_id = e.id AND es.activo = 1 AND es.visible_chatbot = 1
             JOIN servicios s ON s.id = es.servicio_id AND s.activo = 1
             WHERE e.usuario_id = ? AND e.activo = 1 AND e.autorizado = 1
             ORDER BY suc.nombre, s.nombre",
            [$userId]
        );
    }

    private function getBranches(int $userId): array {
        return $this->db->fetchAll(
            "SELECT DISTINCT s.id, s.nombre, s.direccion, s.ciudad, s.estado
             FROM especialistas e
             JOIN sucursales s ON s.id = e.sucursal_id AND s.activo = 1 AND s.autorizado = 1
             WHERE e.usuario_id = ? AND e.activo = 1 AND e.autorizado = 1
             ORDER BY s.nombre",
            [$userId]
        );
    }

    private function getSchedules(int $userId): array {
        $rows = $this->db->fetchAll(
            "SELECT DISTINCT s.id AS sucursal_id, s.nombre AS sucursal, h.dia_semana, h.hora_inicio, h.hora_fin
             FROM especialistas e
             JOIN sucursales s ON s.id = e.sucursal_id
             JOIN horarios_especialistas h ON h.especialista_id = e.id AND h.activo = 1
             WHERE e.usuario_id = ? AND e.activo = 1 AND e.autorizado = 1 AND s.activo = 1 AND s.autorizado = 1
             ORDER BY s.nombre, h.dia_semana, h.hora_inicio",
            [$userId]
        );

        $days = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miercoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sabado', 7 => 'Domingo'];
        return array_map(static function ($row) use ($days) {
            $day = $days[(int)$row['dia_semana']] ?? 'Horario';
            return [
                'sucursal_id' => (int)$row['sucursal_id'],
                'sucursal' => $row['sucursal'],
                'etiqueta' => $day,
                'horario' => substr($row['hora_inicio'], 0, 5) . ' - ' . substr($row['hora_fin'], 0, 5),
            ];
        }, $rows);
    }

    private function validatedForm(
        array $user,
        array $specialist,
        array $branches,
        array $defaultLocationUrls,
        string $defaultSlug
    ): array {
        $fullName = trim($user['nombre'] . ' ' . $user['apellidos']);
        $postedSlug = $_POST['slug'] ?? $defaultSlug;
        $postedSlug = is_string($postedSlug) ? $postedSlug : '';
        $form = [
            'slug' => mb_substr(trim($postedSlug), 0, 100),
            'nombre' => mb_substr(trim($_POST['nombre'] ?? $fullName), 0, 200),
            'titulo_pagina' => mb_substr(trim($_POST['titulo_pagina'] ?? ''), 0, 200),
            'texto_encabezado' => mb_substr(trim($_POST['texto_encabezado'] ?? ''), 0, 300),
            'color_primario' => $this->validHex($_POST['color_primario'] ?? '', '#0f766e'),
            'color_fondo' => $this->validHex($_POST['color_fondo'] ?? '', '#f0fdfa'),
            'logo_url' => $this->validOptionalUrl($_POST['logo_url'] ?? '', 'logo'),
            'instagram_url' => $this->validOptionalUrl($_POST['instagram_url'] ?? '', 'Instagram'),
            'facebook_url' => $this->validOptionalUrl($_POST['facebook_url'] ?? '', 'Facebook'),
            'ubicacion_urls' => [],
        ];

        $postedLocationUrls = is_array($_POST['ubicacion_url'] ?? null) ? $_POST['ubicacion_url'] : [];
        foreach ($branches as $branch) {
            $branchId = (int)$branch['id'];
            $url = $postedLocationUrls[$branchId] ?? ($defaultLocationUrls[$branchId] ?? '');
            $url = is_string($url) ? $url : '';
            $form['ubicacion_urls'][$branchId] = $this->validOptionalUrl($url, 'ubicacion de ' . $branch['nombre']);
        }

        if ($form['nombre'] === '') {
            throw new InvalidArgumentException('El nombre es obligatorio.');
        }
        if ($form['slug'] === '') {
            throw new InvalidArgumentException('El nombre de la carpeta es obligatorio.');
        }
        if ($form['titulo_pagina'] === '') {
            $form['titulo_pagina'] = 'Reserva con ' . $fullName;
        }
        if ($form['texto_encabezado'] === '') {
            $form['texto_encabezado'] = trim(($specialist['profesion'] ?? '') . ' ' . ($specialist['especialidad'] ?? ''));
        }

        return $form;
    }

    private function getLandingLocationUrls(array $status, array $branches): array {
        $urls = [];
        $branchIdsByName = [];
        foreach ($branches as $branch) {
            $branchId = (int)$branch['id'];
            $urls[$branchId] = $this->defaultLocationUrl($branch);
            $branchIdsByName[mb_strtolower(trim($branch['nombre']))] = $branchId;
        }

        if (!$status['available'] || empty($status['landing']['id'])) {
            return $urls;
        }

        try {
            foreach (getBotones((int)$status['landing']['id']) as $button) {
                if (($button['tipo'] ?? '') !== 'dropdown') continue;
                foreach (getDropdownItems((int)$button['id']) as $item) {
                    if (($item['tipo'] ?? '') !== 'link' || empty($item['url'])) continue;

                    $branchId = 0;
                    if (preg_match('/^AIDE_BRANCH:(\d+)$/', (string)($item['valor'] ?? ''), $matches)) {
                        $branchId = (int)$matches[1];
                    } else {
                        $branchId = $branchIdsByName[mb_strtolower(trim($item['texto'] ?? ''))] ?? 0;
                    }

                    if (array_key_exists($branchId, $urls)) {
                        $urls[$branchId] = $item['url'];
                    }
                }
            }
        } catch (Throwable $e) {
            error_log('AIDE landing locations: ' . $e->getMessage());
        }

        return $urls;
    }

    private function defaultLocationUrl(array $branch): string {
        $address = trim(implode(', ', array_filter([
            $branch['direccion'] ?? '',
            $branch['ciudad'] ?? '',
            $branch['estado'] ?? '',
        ])));

        return $address === ''
            ? ''
            : 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($address);
    }

    private function groupContentByBranch(
        array $branches,
        array $services,
        array $schedules,
        array $locationUrls
    ): array {
        $groups = [];
        foreach ($branches as $branch) {
            $branchId = (int)$branch['id'];
            $groups[$branchId] = [
                'branch' => $branch,
                'services' => [],
                'schedules' => [],
                'location_url' => $locationUrls[$branchId] ?? '',
            ];
        }

        foreach ($services as $service) {
            $branchId = (int)($service['sucursal_id'] ?? 0);
            if (isset($groups[$branchId])) $groups[$branchId]['services'][] = $service;
        }
        foreach ($schedules as $schedule) {
            $branchId = (int)($schedule['sucursal_id'] ?? 0);
            if (isset($groups[$branchId])) $groups[$branchId]['schedules'][] = $schedule;
        }

        return array_values($groups);
    }

    private function validHex(string $value, string $default): string {
        return preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? strtolower($value) : $default;
    }

    private function validOptionalUrl(string $value, string $label): string {
        $value = trim($value);
        if ($value === '') return '';
        if (!filter_var($value, FILTER_VALIDATE_URL) || !in_array(parse_url($value, PHP_URL_SCHEME), ['http', 'https'], true)) {
            throw new InvalidArgumentException('La URL de ' . $label . ' no es valida.');
        }
        return mb_substr($value, 0, 500);
    }

    private function validateCsrf(string $token): bool {
        return !empty($_SESSION['landing_csrf']) && hash_equals($_SESSION['landing_csrf'], $token);
    }

    private function normalizeBackground(string $value): string {
        return preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? strtolower($value) : '#f0fdfa';
    }

    private function getLandingSocials(array $status): array {
        $result = ['instagram' => '', 'facebook' => ''];
        if (!$status['available'] || empty($status['landing']['id'])) {
            return $result;
        }

        try {
            foreach (getRedesSociales((int)$status['landing']['id']) as $social) {
                if (array_key_exists($social['tipo'], $result)) {
                    $result[$social['tipo']] = $social['url'];
                }
            }
        } catch (Throwable $e) {
            error_log('AIDE landing socials: ' . $e->getMessage());
        }

        return $result;
    }
}
