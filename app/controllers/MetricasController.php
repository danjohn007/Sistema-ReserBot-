<?php
/**
 * ReserBot - Controlador de Métricas
 */

require_once __DIR__ . '/BaseController.php';

class MetricasController extends BaseController {

    /**
     * Métricas de Origen de Reservas
     */
    public function origenReservas() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN, ROLE_SPECIALIST]);

        $user  = currentUser();
        $rolId = $user['rol_id'];

        // ─── Filtros ────────────────────────────────────────────────────────
        $periodo       = $this->get('periodo') ?: '30d';
        $fechaInicio   = $this->get('fecha_inicio') ?: '';
        $fechaFin      = $this->get('fecha_fin') ?: '';
        $sourceFilter  = $this->get('source') ?: '';
        $especialistaId = (int)($this->get('especialista_id') ?: 0);

        // Resolver rango de fechas según el preset
        if ($periodo === 'custom' && $fechaInicio && $fechaFin) {
            // rango ya definido por el usuario
        } else {
            $fechaFin = date('Y-m-d');
            switch ($periodo) {
                case '7d':  $fechaInicio = date('Y-m-d', strtotime('-6 days'));    break;
                case '30d': $fechaInicio = date('Y-m-d', strtotime('-29 days'));   break;
                case '3m':  $fechaInicio = date('Y-m-d', strtotime('-3 months'));  break;
                case '6m':  $fechaInicio = date('Y-m-d', strtotime('-6 months'));  break;
                case 'mes': $fechaInicio = date('Y-m-01');                         break;
                case 'ano': $fechaInicio = date('Y-01-01');                        break;
                default:    $fechaInicio = date('Y-m-d', strtotime('-29 days'));   break;
            }
        }

        // ─── WHERE base ──────────────────────────────────────────────────────
        $where  = "WHERE r.fecha_cita BETWEEN ? AND ?";
        $params = [$fechaInicio, $fechaFin];

        // Restricciones por rol
        if ($rolId == ROLE_BRANCH_ADMIN) {
            $where   .= " AND r.sucursal_id = ?";
            $params[] = $user['sucursal_id'];
        } elseif ($rolId == ROLE_SPECIALIST) {
            $esp = $this->db->fetch(
                "SELECT id FROM especialistas WHERE usuario_id = ?",
                [$user['id']]
            );
            if ($esp) {
                $where   .= " AND r.especialista_id = ?";
                $params[] = $esp['id'];
            }
        } elseif ($especialistaId > 0) {
            $where   .= " AND r.especialista_id = ?";
            $params[] = $especialistaId;
        }

        // Filtro de origen
        if ($sourceFilter !== '') {
            if ($sourceFilter === 'directo') {
                $where .= " AND (r.source IS NULL OR r.source = '')";
            } else {
                $where   .= " AND r.source = ?";
                $params[] = $sourceFilter;
            }
        }

        // ─── Totales por origen ──────────────────────────────────────────────
        $bySource = $this->db->fetchAll("
            SELECT
                CASE WHEN (r.source IS NULL OR r.source = '') THEN 'directo' ELSE r.source END as source,
                COUNT(*) as total,
                SUM(CASE WHEN r.estado = 'completada'  THEN 1 ELSE 0 END) as completadas,
                SUM(CASE WHEN r.estado = 'cancelada'   THEN 1 ELSE 0 END) as canceladas,
                SUM(CASE WHEN r.estado = 'pendiente'   THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN r.primera_consulta = 1   THEN 1 ELSE 0 END) as primera_consulta
            FROM reservaciones r
            $where
            GROUP BY CASE WHEN (r.source IS NULL OR r.source = '') THEN 'directo' ELSE r.source END
            ORDER BY total DESC
        ", $params);

        $totalReservas = array_sum(array_column($bySource, 'total'));

        // ─── Serie temporal agrupada por día y origen ────────────────────────
        $byDay = $this->db->fetchAll("
            SELECT
                DATE(r.fecha_cita) as dia,
                CASE WHEN (r.source IS NULL OR r.source = '') THEN 'directo' ELSE r.source END as source,
                COUNT(*) as total
            FROM reservaciones r
            $where
            GROUP BY DATE(r.fecha_cita),
                     CASE WHEN (r.source IS NULL OR r.source = '') THEN 'directo' ELSE r.source END
            ORDER BY dia ASC
        ", $params);

        // ─── Resumen semanal / mensual (para la gráfica de barras agrupada) ──
        $groupBy = (strtotime($fechaFin) - strtotime($fechaInicio)) <= 86400 * 31
            ? "DATE(r.fecha_cita)"
            : "DATE_FORMAT(r.fecha_cita, '%Y-%m-01')";

        $byPeriod = $this->db->fetchAll("
            SELECT
                $groupBy as periodo,
                CASE WHEN (r.source IS NULL OR r.source = '') THEN 'directo' ELSE r.source END as source,
                COUNT(*) as total
            FROM reservaciones r
            $where
            GROUP BY $groupBy,
                     CASE WHEN (r.source IS NULL OR r.source = '') THEN 'directo' ELSE r.source END
            ORDER BY periodo ASC
        ", $params);

        // ─── Lista de especialistas (filtro solo para admins) ────────────────
        $especialistas = [];
        if ($rolId == ROLE_SUPERADMIN || $rolId == ROLE_BRANCH_ADMIN) {
            $espWhere  = '';
            $espParams = [];
            if ($rolId == ROLE_BRANCH_ADMIN) {
                $espWhere    = "WHERE e.sucursal_id = ?";
                $espParams[] = $user['sucursal_id'];
            }
            $especialistas = $this->db->fetchAll("
                SELECT e.id, u.nombre, u.apellidos
                FROM especialistas e
                JOIN usuarios u ON e.usuario_id = u.id
                $espWhere
                ORDER BY u.nombre, u.apellidos
            ", $espParams);
        }

        // ─── Tendencia: primera vs recurrente por origen ─────────────────────
        $primeraVsRecurrente = $this->db->fetchAll("
            SELECT
                CASE WHEN (r.source IS NULL OR r.source = '') THEN 'directo' ELSE r.source END as source,
                SUM(CASE WHEN r.primera_consulta = 1 THEN 1 ELSE 0 END) as primera,
                SUM(CASE WHEN r.primera_consulta = 0 THEN 1 ELSE 0 END) as recurrente
            FROM reservaciones r
            $where
            GROUP BY CASE WHEN (r.source IS NULL OR r.source = '') THEN 'directo' ELSE r.source END
        ", $params);

        $this->render('metricas/origen', [
            'title'              => 'Métricas de Origen de Reservas',
            'bySource'           => $bySource,
            'byDay'              => $byDay,
            'byPeriod'           => $byPeriod,
            'totalReservas'      => $totalReservas,
            'periodo'            => $periodo,
            'fechaInicio'        => $fechaInicio,
            'fechaFin'           => $fechaFin,
            'sourceFilter'       => $sourceFilter,
            'especialistaId'     => $especialistaId,
            'especialistas'      => $especialistas,
            'primeraVsRecurrente'=> $primeraVsRecurrente,
            'rolId'              => $rolId,
        ]);
    }
}
