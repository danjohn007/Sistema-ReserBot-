<?php
/**
 * Controlador para la vista pública de registros interesados
 * No requiere autenticación
 */

require_once __DIR__ . '/BaseController.php';

class InteresadosController extends BaseController
{
    /**
     * Muestra la lista pública de registros interesados
     * Accesible sin autenticación
     */
    public function index()
    {
        // Obtener todos los registros de interesados
        try {
            $registros = $this->db->fetchAll(
                "SELECT id, nombre, email, telefono, estado, notas, created_at, updated_at 
                 FROM registros_interesados 
                 ORDER BY created_at DESC"
            );
            
            // Calcular estadísticas por estado
            $estadisticas = [
                'total' => count($registros),
                'pendiente' => 0,
                'contactado' => 0,
                'completado' => 0,
                'rechazado' => 0
            ];
            
            foreach ($registros as $registro) {
                $estado = strtolower($registro['estado']);
                if (isset($estadisticas[$estado])) {
                    $estadisticas[$estado]++;
                }
            }
            
        } catch (Exception $e) {
            $error = "Error al cargar los registros: " . $e->getMessage();
            $registros = [];
            $estadisticas = ['total' => 0, 'pendiente' => 0, 'contactado' => 0, 'completado' => 0, 'rechazado' => 0];
        }
        
        // Cargar la vista pública (sin layout de autenticación)
        include VIEWS_PATH . '/interesados/index.php';
    }

    /**
     * Marca un registro como "contactado" via AJAX
     */
    public function marcarContactado()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if (!$id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            return;
        }

        try {
            $this->db->update(
                "UPDATE registros_interesados SET estado = 'contactado', updated_at = NOW() WHERE id = ? AND estado = 'pendiente'",
                [$id]
            );
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
        }
    }
}
