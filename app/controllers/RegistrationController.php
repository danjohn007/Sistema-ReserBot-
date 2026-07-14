<?php
/**
 * Captura de solicitudes conjuntas de sucursales y profesionistas.
 */

require_once __DIR__ . '/BaseController.php';

class RegistrationController extends BaseController {

    public function index() {
        $this->requireRole(ROLE_REGISTRATION);

        $user = currentUser();
        $requests = $this->db->fetchAll(
            "SELECT sr.*,
                    (SELECT GROUP_CONCAT(s.nombre ORDER BY s.nombre SEPARATOR '|||')
                     FROM solicitudes_registro_sucursales srs
                     JOIN sucursales s ON s.id = srs.sucursal_id
                     WHERE srs.solicitud_id = sr.id) AS sucursales_nombres,
                    (SELECT COUNT(*)
                     FROM solicitudes_registro_sucursales srs
                     WHERE srs.solicitud_id = sr.id) AS total_sucursales,
                    (SELECT COUNT(DISTINCT e.usuario_id)
                     FROM solicitudes_registro_sucursales srs
                     JOIN especialistas e ON e.sucursal_id = srs.sucursal_id
                     WHERE srs.solicitud_id = sr.id) AS total_profesionistas
             FROM solicitudes_registro sr
             WHERE sr.creado_por = ?
             ORDER BY sr.created_at DESC",
            [$user['id']]
        );

        $this->render('registration/index', [
            'title' => 'Solicitudes de registro',
            'requests' => $requests
        ]);
    }

    public function create() {
        $this->requireRole(ROLE_REGISTRATION);

        $error = '';
        $branches = [0 => $this->emptyBranch()];
        $professionals = [$this->emptyProfessional([0])];
        $passwords = [];

        if ($this->isPost()) {
            $rawBranches = isset($_POST['sucursales']) && is_array($_POST['sucursales'])
                ? array_slice($_POST['sucursales'], 0, 10, true)
                : [];
            $branches = [];

            foreach ($rawBranches as $branchIndex => $rawBranch) {
                if (!is_array($rawBranch)) {
                    continue;
                }

                $branches[(string) $branchIndex] = [
                    'nombre' => $this->text($rawBranch['nombre'] ?? '', 150),
                    'direccion' => $this->text($rawBranch['direccion'] ?? '', 500),
                    'ciudad' => $this->text($rawBranch['ciudad'] ?? '', 100),
                    'estado' => $this->text($rawBranch['estado'] ?? '', 100),
                    'codigo_postal' => $this->text($rawBranch['codigo_postal'] ?? '', 10),
                    'telefono' => $this->text($rawBranch['telefono'] ?? '', 20),
                    'email' => strtolower($this->text($rawBranch['email'] ?? '', 150)),
                    'horario_apertura' => $this->text($rawBranch['horario_apertura'] ?? '', 5),
                    'horario_cierre' => $this->text($rawBranch['horario_cierre'] ?? '', 5)
                ];
            }

            $rawProfessionals = isset($_POST['profesionistas']) && is_array($_POST['profesionistas'])
                ? array_slice($_POST['profesionistas'], 0, 10, true)
                : [];
            $professionals = [];

            foreach ($rawProfessionals as $rawProfessional) {
                if (!is_array($rawProfessional)) {
                    continue;
                }

                $selectedBranches = isset($rawProfessional['sucursales']) && is_array($rawProfessional['sucursales'])
                    ? array_values(array_unique(array_map('strval', $rawProfessional['sucursales'])))
                    : [];
                $professionals[] = [
                    'nombre' => $this->text($rawProfessional['nombre'] ?? '', 100),
                    'apellidos' => $this->text($rawProfessional['apellidos'] ?? '', 100),
                    'email' => strtolower($this->text($rawProfessional['email'] ?? '', 150)),
                    'telefono' => $this->text($rawProfessional['telefono'] ?? '', 20),
                    'profesion' => $this->text($rawProfessional['profesion'] ?? '', 100),
                    'especialidad' => $this->text($rawProfessional['especialidad'] ?? '', 150),
                    'descripcion' => $this->text($rawProfessional['descripcion'] ?? '', 1000),
                    'experiencia_anos' => (int) ($rawProfessional['experiencia_anos'] ?? 0),
                    'tarifa_base' => is_numeric($rawProfessional['tarifa_base'] ?? null)
                        ? max(0, (float) $rawProfessional['tarifa_base'])
                        : 0,
                    'sucursales' => $selectedBranches
                ];
                $passwords[] = (string) ($rawProfessional['password'] ?? '');
            }

            $error = $this->validateRequest($branches, $professionals, $passwords);

            if ($error === '') {
                $user = currentUser();
                $this->db->beginTransaction();

                try {
                    $branchIds = [];
                    foreach ($branches as $branchIndex => $branch) {
                        $branchIds[(string) $branchIndex] = $this->db->insert(
                            "INSERT INTO sucursales
                             (nombre, direccion, ciudad, estado, codigo_postal, telefono, email,
                              horario_apertura, horario_cierre, color, activo, autorizado)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, '#3B82F6', 0, 0)",
                            [
                                $branch['nombre'], $branch['direccion'], $branch['ciudad'],
                                $branch['estado'], $branch['codigo_postal'], $branch['telefono'],
                                $branch['email'], $branch['horario_apertura'] ?: '08:00',
                                $branch['horario_cierre'] ?: '20:00'
                            ]
                        );
                    }

                    $primaryBranchId = reset($branchIds);
                    $requestId = $this->db->insert(
                        "INSERT INTO solicitudes_registro (sucursal_id, creado_por, estado)
                         VALUES (?, ?, 'pendiente')",
                        [$primaryBranchId, $user['id']]
                    );

                    foreach ($branchIds as $branchId) {
                        $this->db->insert(
                            "INSERT INTO solicitudes_registro_sucursales (solicitud_id, sucursal_id)
                             VALUES (?, ?)",
                            [$requestId, $branchId]
                        );
                    }

                    foreach ($professionals as $index => $professional) {
                        $selectedBranchIds = [];
                        foreach ($professional['sucursales'] as $branchIndex) {
                            $selectedBranchIds[] = $branchIds[(string) $branchIndex];
                        }

                        $professionalUserId = $this->db->insert(
                            "INSERT INTO usuarios
                             (nombre, apellidos, email, telefono, password, rol_id, sucursal_id,
                              email_verificado, activo)
                             VALUES (?, ?, ?, ?, ?, ?, ?, 1, 0)",
                            [
                                $professional['nombre'], $professional['apellidos'],
                                $professional['email'], $professional['telefono'],
                                password_hash($passwords[$index], PASSWORD_DEFAULT),
                                ROLE_SPECIALIST, reset($selectedBranchIds)
                            ]
                        );

                        foreach ($selectedBranchIds as $branchId) {
                            $this->db->insert(
                                "INSERT INTO especialistas
                                 (usuario_id, sucursal_id, profesion, especialidad, descripcion,
                                  experiencia_anos, tarifa_base, activo, autorizado)
                                 VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0)",
                                [
                                    $professionalUserId, $branchId, $professional['profesion'],
                                    $professional['especialidad'], $professional['descripcion'],
                                    $professional['experiencia_anos'], $professional['tarifa_base']
                                ]
                            );
                        }
                    }

                    $this->db->commit();
                    logAction(
                        'registration_request_create',
                        'Solicitud de registro creada: #' . $requestId . ' - ' . count($branches) . ' sucursales'
                    );
                    setFlashMessage('success', 'Solicitud enviada. Las sucursales y sus profesionistas quedaron pendientes de autorizacion.');
                    redirect('/solicitudes-registro/ver?id=' . $requestId);
                } catch (Exception $e) {
                    $this->db->rollBack();
                    error_log('Error al crear solicitud de registro: ' . $e->getMessage());
                    $error = 'No se pudo guardar la solicitud. Revisa que los correos no esten registrados e intenta de nuevo.';
                }
            }
        }

        $this->render('registration/create', [
            'title' => 'Nueva solicitud',
            'error' => $error,
            'branches' => $branches,
            'professionals' => $professionals
        ]);
    }

    public function view() {
        $this->requireRole(ROLE_REGISTRATION);

        $id = (int) $this->get('id');
        $user = currentUser();
        $request = $this->db->fetch(
            "SELECT sr.*
             FROM solicitudes_registro sr
             WHERE sr.id = ? AND sr.creado_por = ?",
            [$id, $user['id']]
        );

        if (!$request) {
            setFlashMessage('error', 'Solicitud no encontrada.');
            redirect('/solicitudes-registro');
        }

        $branches = $this->getRequestBranches($id);
        $professionals = $this->getRequestProfessionals($id);

        $this->render('registration/view', [
            'title' => 'Solicitud #' . $request['id'],
            'request' => $request,
            'branches' => $branches,
            'professionals' => $professionals
        ]);
    }

    private function validateRequest($branches, $professionals, $passwords) {
        if (empty($branches)) {
            return 'Agrega al menos una sucursal a la solicitud.';
        }

        foreach ($branches as $index => $branch) {
            $number = array_search($index, array_keys($branches), true) + 1;
            if ($branch['nombre'] === '' || $branch['direccion'] === '' || $branch['ciudad'] === ''
                || $branch['estado'] === '' || $branch['telefono'] === '' || $branch['email'] === '') {
                return 'Completa todos los campos obligatorios de la sucursal ' . $number . '.';
            }

            if (!validateEmail($branch['email'])) {
                return 'El correo de la sucursal ' . $number . ' no es valido.';
            }

            if ($branch['horario_apertura'] && $branch['horario_cierre']
                && $branch['horario_apertura'] >= $branch['horario_cierre']) {
                return 'El horario de cierre de la sucursal ' . $number . ' debe ser posterior al de apertura.';
            }
        }

        if (empty($professionals)) {
            return 'Agrega al menos un profesionista a la solicitud.';
        }

        $emails = [];
        $branchCoverage = array_fill_keys(array_map('strval', array_keys($branches)), 0);
        foreach ($professionals as $index => $professional) {
            $number = $index + 1;
            if ($professional['nombre'] === '' || $professional['apellidos'] === ''
                || $professional['email'] === '' || $professional['telefono'] === ''
                || $professional['profesion'] === '') {
                return 'Completa los campos obligatorios del profesionista ' . $number . '.';
            }

            if (!validateEmail($professional['email'])) {
                return 'El correo del profesionista ' . $number . ' no es valido.';
            }

            if (strlen($passwords[$index] ?? '') < 8) {
                return 'La contrasena del profesionista ' . $number . ' debe tener al menos 8 caracteres.';
            }

            if ($professional['experiencia_anos'] < 0 || $professional['experiencia_anos'] > 80) {
                return 'Los anos de experiencia del profesionista ' . $number . ' no son validos.';
            }

            if (empty($professional['sucursales'])) {
                return 'Selecciona al menos una sucursal para el profesionista ' . $number . '.';
            }

            foreach ($professional['sucursales'] as $branchIndex) {
                $branchIndex = (string) $branchIndex;
                if (!array_key_exists($branchIndex, $branchCoverage)) {
                    return 'La asignacion de sucursales del profesionista ' . $number . ' no es valida.';
                }
                $branchCoverage[$branchIndex]++;
            }

            if (isset($emails[$professional['email']])) {
                return 'No puedes repetir el mismo correo en dos profesionistas.';
            }
            $emails[$professional['email']] = true;

            $exists = $this->db->fetch(
                "SELECT id FROM usuarios WHERE email = ?",
                [$professional['email']]
            );
            if ($exists) {
                return 'El correo ' . $professional['email'] . ' ya esta registrado en el sistema.';
            }
        }

        foreach ($branchCoverage as $branchIndex => $total) {
            if ($total === 0) {
                $branchNumber = array_search($branchIndex, array_map('strval', array_keys($branches)), true) + 1;
                return 'Asigna al menos un profesionista a la sucursal ' . $branchNumber . '.';
            }
        }

        return '';
    }

    private function getRequestBranches($requestId) {
        return $this->db->fetchAll(
            "SELECT s.*
             FROM solicitudes_registro_sucursales srs
             JOIN sucursales s ON s.id = srs.sucursal_id
             WHERE srs.solicitud_id = ?
             ORDER BY srs.created_at, s.id",
            [$requestId]
        );
    }

    private function getRequestProfessionals($requestId) {
        return $this->db->fetchAll(
            "SELECT e.usuario_id, e.profesion, e.especialidad, e.descripcion,
                    e.experiencia_anos, e.tarifa_base, MIN(e.autorizado) AS autorizado,
                    u.nombre, u.apellidos, u.email, u.telefono,
                    GROUP_CONCAT(DISTINCT s.nombre ORDER BY s.nombre SEPARATOR '|||') AS sucursales_nombres
             FROM solicitudes_registro_sucursales srs
             JOIN sucursales s ON s.id = srs.sucursal_id
             JOIN especialistas e ON e.sucursal_id = s.id
             JOIN usuarios u ON u.id = e.usuario_id
             WHERE srs.solicitud_id = ?
             GROUP BY e.usuario_id, e.profesion, e.especialidad, e.descripcion,
                      e.experiencia_anos, e.tarifa_base, u.nombre, u.apellidos, u.email, u.telefono
             ORDER BY u.nombre, u.apellidos",
            [$requestId]
        );
    }

    private function emptyBranch() {
        return [
            'nombre' => '',
            'direccion' => '',
            'ciudad' => '',
            'estado' => 'Queretaro',
            'codigo_postal' => '',
            'telefono' => '',
            'email' => '',
            'horario_apertura' => '08:00',
            'horario_cierre' => '20:00'
        ];
    }

    private function emptyProfessional($branches = []) {
        return [
            'nombre' => '',
            'apellidos' => '',
            'email' => '',
            'telefono' => '',
            'profesion' => '',
            'especialidad' => '',
            'descripcion' => '',
            'experiencia_anos' => 0,
            'tarifa_base' => 0,
            'sucursales' => array_map('strval', $branches)
        ];
    }

    private function text($value, $maxLength) {
        $value = trim(strip_tags((string) $value));
        return mb_substr($value, 0, $maxLength, 'UTF-8');
    }
}
