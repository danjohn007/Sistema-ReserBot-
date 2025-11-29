<?php
/**
 * ReserBot - Controlador de Reportes
 */

require_once __DIR__ . '/BaseController.php';

class ReportController extends BaseController {
    
    /**
     * Dashboard de reportes
     */
    public function index() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $user = currentUser();
        
        $this->render('reports/index', [
            'title' => 'Reportes y Estadísticas'
        ]);
    }
    
    /**
     * Reporte de citas
     */
    public function appointments() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $user = currentUser();
        
        $fechaInicio = $this->get('fecha_inicio') ?: date('Y-m-01');
        $fechaFin = $this->get('fecha_fin') ?: date('Y-m-t');
        $sucursal_id = $this->get('sucursal_id');
        
        $filters = [$fechaInicio, $fechaFin];
        $sql = "SELECT r.fecha_cita, r.estado, COUNT(*) as total,
                       SUM(CASE WHEN r.estado = 'completada' THEN r.precio_total ELSE 0 END) as ingresos
                FROM reservaciones r
                WHERE r.fecha_cita BETWEEN ? AND ?";
        
        if ($user['rol_id'] == ROLE_BRANCH_ADMIN) {
            $sql .= " AND r.sucursal_id = ?";
            $filters[] = $user['sucursal_id'];
        } elseif ($sucursal_id) {
            $sql .= " AND r.sucursal_id = ?";
            $filters[] = $sucursal_id;
        }
        
        $sql .= " GROUP BY r.fecha_cita, r.estado ORDER BY r.fecha_cita";
        
        $data = $this->db->fetchAll($sql, $filters);
        
        // Resumen por estado
        $filtersSummary = [$fechaInicio, $fechaFin];
        $sqlSummary = "SELECT r.estado, COUNT(*) as total
                       FROM reservaciones r
                       WHERE r.fecha_cita BETWEEN ? AND ?";
        
        if ($user['rol_id'] == ROLE_BRANCH_ADMIN) {
            $sqlSummary .= " AND r.sucursal_id = ?";
            $filtersSummary[] = $user['sucursal_id'];
        } elseif ($sucursal_id) {
            $sqlSummary .= " AND r.sucursal_id = ?";
            $filtersSummary[] = $sucursal_id;
        }
        
        $sqlSummary .= " GROUP BY r.estado";
        
        $summary = $this->db->fetchAll($sqlSummary, $filtersSummary);
        
        // Sucursales para filtro
        $branches = [];
        if ($user['rol_id'] == ROLE_SUPERADMIN) {
            $branches = $this->db->fetchAll("SELECT id, nombre FROM sucursales WHERE activo = 1 ORDER BY nombre");
        }
        
        $this->render('reports/appointments', [
            'title' => 'Reporte de Citas',
            'data' => $data,
            'summary' => $summary,
            'branches' => $branches,
            'filters' => [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'sucursal_id' => $sucursal_id
            ]
        ]);
    }
    
    /**
     * Reporte de ingresos
     */
    public function income() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $user = currentUser();
        
        $fechaInicio = $this->get('fecha_inicio') ?: date('Y-m-01');
        $fechaFin = $this->get('fecha_fin') ?: date('Y-m-t');
        $sucursal_id = $this->get('sucursal_id');
        
        $filters = [$fechaInicio, $fechaFin];
        
        // Ingresos por día
        $sqlDaily = "SELECT DATE(r.fecha_cita) as fecha, SUM(r.precio_total) as total
                     FROM reservaciones r
                     WHERE r.estado = 'completada' AND r.fecha_cita BETWEEN ? AND ?";
        
        if ($user['rol_id'] == ROLE_BRANCH_ADMIN) {
            $sqlDaily .= " AND r.sucursal_id = ?";
            $filters[] = $user['sucursal_id'];
        } elseif ($sucursal_id) {
            $sqlDaily .= " AND r.sucursal_id = ?";
            $filters[] = $sucursal_id;
        }
        
        $sqlDaily .= " GROUP BY DATE(r.fecha_cita) ORDER BY fecha";
        
        $dailyIncome = $this->db->fetchAll($sqlDaily, $filters);
        
        // Ingresos por servicio
        $filters2 = [$fechaInicio, $fechaFin];
        $sqlByService = "SELECT s.nombre as servicio, SUM(r.precio_total) as total, COUNT(*) as cantidad
                         FROM reservaciones r
                         JOIN servicios s ON r.servicio_id = s.id
                         WHERE r.estado = 'completada' AND r.fecha_cita BETWEEN ? AND ?";
        
        if ($user['rol_id'] == ROLE_BRANCH_ADMIN) {
            $sqlByService .= " AND r.sucursal_id = ?";
            $filters2[] = $user['sucursal_id'];
        } elseif ($sucursal_id) {
            $sqlByService .= " AND r.sucursal_id = ?";
            $filters2[] = $sucursal_id;
        }
        
        $sqlByService .= " GROUP BY s.id ORDER BY total DESC";
        
        $incomeByService = $this->db->fetchAll($sqlByService, $filters2);
        
        // Total
        $filters3 = [$fechaInicio, $fechaFin];
        $sqlTotal = "SELECT SUM(r.precio_total) as total, COUNT(*) as cantidad
                     FROM reservaciones r
                     WHERE r.estado = 'completada' AND r.fecha_cita BETWEEN ? AND ?";
        
        if ($user['rol_id'] == ROLE_BRANCH_ADMIN) {
            $sqlTotal .= " AND r.sucursal_id = ?";
            $filters3[] = $user['sucursal_id'];
        } elseif ($sucursal_id) {
            $sqlTotal .= " AND r.sucursal_id = ?";
            $filters3[] = $sucursal_id;
        }
        
        $totals = $this->db->fetch($sqlTotal, $filters3);
        
        // Sucursales para filtro
        $branches = [];
        if ($user['rol_id'] == ROLE_SUPERADMIN) {
            $branches = $this->db->fetchAll("SELECT id, nombre FROM sucursales WHERE activo = 1 ORDER BY nombre");
        }
        
        $this->render('reports/income', [
            'title' => 'Reporte de Ingresos',
            'dailyIncome' => $dailyIncome,
            'incomeByService' => $incomeByService,
            'totals' => $totals,
            'branches' => $branches,
            'filters' => [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'sucursal_id' => $sucursal_id
            ]
        ]);
    }
    
    /**
     * Reporte de especialistas
     */
    public function specialists() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $user = currentUser();
        
        $fechaInicio = $this->get('fecha_inicio') ?: date('Y-m-01');
        $fechaFin = $this->get('fecha_fin') ?: date('Y-m-t');
        
        $filters = [$fechaInicio, $fechaFin];
        
        $sql = "SELECT e.id, u.nombre, u.apellidos, s.nombre as sucursal,
                       COUNT(r.id) as total_citas,
                       SUM(CASE WHEN r.estado = 'completada' THEN 1 ELSE 0 END) as completadas,
                       SUM(CASE WHEN r.estado = 'cancelada' THEN 1 ELSE 0 END) as canceladas,
                       SUM(CASE WHEN r.estado = 'completada' THEN r.precio_total ELSE 0 END) as ingresos,
                       e.calificacion_promedio
                FROM especialistas e
                JOIN usuarios u ON e.usuario_id = u.id
                JOIN sucursales s ON e.sucursal_id = s.id
                LEFT JOIN reservaciones r ON e.id = r.especialista_id AND r.fecha_cita BETWEEN ? AND ?
                WHERE e.activo = 1";
        
        if ($user['rol_id'] == ROLE_BRANCH_ADMIN) {
            $sql .= " AND e.sucursal_id = ?";
            $filters[] = $user['sucursal_id'];
        }
        
        $sql .= " GROUP BY e.id ORDER BY ingresos DESC";
        
        $specialists = $this->db->fetchAll($sql, $filters);
        
        $this->render('reports/specialists', [
            'title' => 'Reporte de Especialistas',
            'specialists' => $specialists,
            'filters' => [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin
            ]
        ]);
    }
    
    /**
     * Exportar reporte
     */
    public function export() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $type = $this->get('type');
        $format = $this->get('format') ?: 'csv';
        
        // Por simplicidad, exportar en CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="reporte_' . $type . '_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // BOM para Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Cabeceras y datos según el tipo
        switch ($type) {
            case 'citas':
                fputcsv($output, ['Código', 'Fecha', 'Hora', 'Cliente', 'Especialista', 'Servicio', 'Estado', 'Precio']);
                
                $reservations = $this->db->fetchAll(
                    "SELECT r.codigo, r.fecha_cita, r.hora_inicio, 
                            CONCAT(u.nombre, ' ', u.apellidos) as cliente,
                            CONCAT(ue.nombre, ' ', ue.apellidos) as especialista,
                            s.nombre as servicio, r.estado, r.precio_total
                     FROM reservaciones r
                     JOIN usuarios u ON r.cliente_id = u.id
                     JOIN especialistas e ON r.especialista_id = e.id
                     JOIN usuarios ue ON e.usuario_id = ue.id
                     JOIN servicios s ON r.servicio_id = s.id
                     ORDER BY r.fecha_cita DESC, r.hora_inicio"
                );
                
                foreach ($reservations as $r) {
                    fputcsv($output, [
                        $r['codigo'],
                        $r['fecha_cita'],
                        $r['hora_inicio'],
                        $r['cliente'],
                        $r['especialista'],
                        $r['servicio'],
                        $r['estado'],
                        $r['precio_total']
                    ]);
                }
                break;
        }
        
        fclose($output);
        exit;
    }
}
