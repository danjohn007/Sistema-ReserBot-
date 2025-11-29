<?php
/**
 * ReserBot - Controlador de Servicios
 */

require_once __DIR__ . '/BaseController.php';

class ServiceController extends BaseController {
    
    /**
     * Lista de servicios
     */
    public function index() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $services = $this->db->fetchAll(
            "SELECT s.*, c.nombre as categoria_nombre, c.color as categoria_color
             FROM servicios s
             JOIN categorias_servicios c ON s.categoria_id = c.id
             ORDER BY c.orden, s.nombre"
        );
        
        $this->render('services/index', [
            'title' => 'Servicios',
            'services' => $services
        ]);
    }
    
    /**
     * Crear servicio
     */
    public function create() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $categories = $this->db->fetchAll(
            "SELECT * FROM categorias_servicios WHERE activo = 1 ORDER BY orden, nombre"
        );
        
        $error = '';
        
        if ($this->isPost()) {
            $categoria_id = $this->post('categoria_id');
            $nombre = $this->post('nombre');
            $descripcion = $this->post('descripcion');
            $duracion_minutos = $this->post('duracion_minutos') ?: 30;
            $precio = $this->post('precio') ?: 0;
            $precio_oferta = $this->post('precio_oferta') ?: null;
            
            if (empty($nombre) || empty($categoria_id)) {
                $error = 'El nombre y la categoría son obligatorios.';
            } else {
                $this->db->insert(
                    "INSERT INTO servicios (categoria_id, nombre, descripcion, duracion_minutos, precio, precio_oferta) 
                     VALUES (?, ?, ?, ?, ?, ?)",
                    [$categoria_id, $nombre, $descripcion, $duracion_minutos, $precio, $precio_oferta]
                );
                
                logAction('service_create', 'Servicio creado: ' . $nombre);
                setFlashMessage('success', 'Servicio creado correctamente.');
                redirect('/servicios');
            }
        }
        
        $this->render('services/create', [
            'title' => 'Nuevo Servicio',
            'categories' => $categories,
            'error' => $error
        ]);
    }
    
    /**
     * Editar servicio
     */
    public function edit() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $id = $this->get('id');
        
        $service = $this->db->fetch("SELECT * FROM servicios WHERE id = ?", [$id]);
        
        if (!$service) {
            setFlashMessage('error', 'Servicio no encontrado.');
            redirect('/servicios');
        }
        
        $categories = $this->db->fetchAll(
            "SELECT * FROM categorias_servicios WHERE activo = 1 ORDER BY orden, nombre"
        );
        
        $error = '';
        
        if ($this->isPost()) {
            $categoria_id = $this->post('categoria_id');
            $nombre = $this->post('nombre');
            $descripcion = $this->post('descripcion');
            $duracion_minutos = $this->post('duracion_minutos') ?: 30;
            $precio = $this->post('precio') ?: 0;
            $precio_oferta = $this->post('precio_oferta') ?: null;
            $activo = $this->post('activo') ? 1 : 0;
            
            if (empty($nombre) || empty($categoria_id)) {
                $error = 'El nombre y la categoría son obligatorios.';
            } else {
                $this->db->update(
                    "UPDATE servicios SET categoria_id = ?, nombre = ?, descripcion = ?, 
                     duracion_minutos = ?, precio = ?, precio_oferta = ?, activo = ? WHERE id = ?",
                    [$categoria_id, $nombre, $descripcion, $duracion_minutos, $precio, $precio_oferta, $activo, $id]
                );
                
                logAction('service_update', 'Servicio actualizado: ' . $nombre);
                setFlashMessage('success', 'Servicio actualizado correctamente.');
                redirect('/servicios');
            }
        }
        
        $this->render('services/edit', [
            'title' => 'Editar Servicio',
            'service' => $service,
            'categories' => $categories,
            'error' => $error
        ]);
    }
    
    /**
     * Eliminar servicio
     */
    public function delete() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $id = $this->get('id');
        
        $service = $this->db->fetch("SELECT nombre FROM servicios WHERE id = ?", [$id]);
        
        if ($service) {
            $this->db->update("UPDATE servicios SET activo = 0 WHERE id = ?", [$id]);
            logAction('service_delete', 'Servicio desactivado: ' . $service['nombre']);
            setFlashMessage('success', 'Servicio desactivado correctamente.');
        }
        
        redirect('/servicios');
    }
    
    /**
     * Lista de categorías
     */
    public function categories() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $categories = $this->db->fetchAll(
            "SELECT c.*, COUNT(s.id) as total_servicios
             FROM categorias_servicios c
             LEFT JOIN servicios s ON c.id = s.categoria_id AND s.activo = 1
             GROUP BY c.id
             ORDER BY c.orden, c.nombre"
        );
        
        $this->render('services/categories', [
            'title' => 'Categorías de Servicios',
            'categories' => $categories
        ]);
    }
    
    /**
     * Crear categoría
     */
    public function createCategory() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $error = '';
        
        if ($this->isPost()) {
            $nombre = $this->post('nombre');
            $descripcion = $this->post('descripcion');
            $icono = $this->post('icono') ?: 'fas fa-concierge-bell';
            $color = $this->post('color') ?: '#3B82F6';
            $orden = $this->post('orden') ?: 0;
            
            if (empty($nombre)) {
                $error = 'El nombre de la categoría es obligatorio.';
            } else {
                $this->db->insert(
                    "INSERT INTO categorias_servicios (nombre, descripcion, icono, color, orden) 
                     VALUES (?, ?, ?, ?, ?)",
                    [$nombre, $descripcion, $icono, $color, $orden]
                );
                
                logAction('category_create', 'Categoría creada: ' . $nombre);
                setFlashMessage('success', 'Categoría creada correctamente.');
                redirect('/categorias');
            }
        }
        
        $this->render('services/create-category', [
            'title' => 'Nueva Categoría',
            'error' => $error
        ]);
    }
    
    /**
     * Editar categoría
     */
    public function editCategory() {
        $this->requireRole([ROLE_SUPERADMIN, ROLE_BRANCH_ADMIN]);
        
        $id = $this->get('id');
        
        $category = $this->db->fetch("SELECT * FROM categorias_servicios WHERE id = ?", [$id]);
        
        if (!$category) {
            setFlashMessage('error', 'Categoría no encontrada.');
            redirect('/categorias');
        }
        
        $error = '';
        
        if ($this->isPost()) {
            $nombre = $this->post('nombre');
            $descripcion = $this->post('descripcion');
            $icono = $this->post('icono') ?: 'fas fa-concierge-bell';
            $color = $this->post('color') ?: '#3B82F6';
            $orden = $this->post('orden') ?: 0;
            $activo = $this->post('activo') ? 1 : 0;
            
            if (empty($nombre)) {
                $error = 'El nombre de la categoría es obligatorio.';
            } else {
                $this->db->update(
                    "UPDATE categorias_servicios SET nombre = ?, descripcion = ?, icono = ?, 
                     color = ?, orden = ?, activo = ? WHERE id = ?",
                    [$nombre, $descripcion, $icono, $color, $orden, $activo, $id]
                );
                
                logAction('category_update', 'Categoría actualizada: ' . $nombre);
                setFlashMessage('success', 'Categoría actualizada correctamente.');
                redirect('/categorias');
            }
        }
        
        $this->render('services/edit-category', [
            'title' => 'Editar Categoría',
            'category' => $category,
            'error' => $error
        ]);
    }
}
