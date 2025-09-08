<?php
// MEJORA: Se asume que los modelos relacionados también se cargarán
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/Attachment.php'; // Necesario para la vista 'show'
require_once __DIR__ . '/../models/Attendance.php'; // Necesario para la vista 'show'
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class EmployeeController {
    private $employeeModel;
    private $attachmentModel;
    private $attendanceModel;
    private $authMiddleware;

    public function __construct() {
        $this->employeeModel = new Employee();
        $this->attachmentModel = new Attachment();
        $this->attendanceModel = new Attendance();
        $this->authMiddleware = new AuthMiddleware();
    }

    /**
     * MEJORA: Unifica la lista de empleados y los resultados de búsqueda.
     * Muestra una lista paginada de empleados, con opción de búsqueda.
     */
    public function index() {
        $this->authMiddleware->requireAuth();
        // MEJORA: El rol 'manager' también debería poder ver la lista.
        $this->authMiddleware->requireAnyRole(['super_admin', 'manager', 'supervisor']);

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search_keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';
        $records_per_page = 10;

        if (!empty($search_keywords)) {
            $employees = $this->employeeModel->search($search_keywords, $page, $records_per_page);
            $total_rows = $this->employeeModel->countSearchResults($search_keywords); // MEJORA: Conteo correcto para búsqueda
        } else {
            $employees = $this->employeeModel->readAll($page, $records_per_page);
            $total_rows = $this->employeeModel->countAll();
        }

        // Prepara los datos para la vista
        $pagination = [
            'current_page' => $page,
            'total_pages' => ceil($total_rows / $records_per_page),
        ];

        // Carga la vista con los datos preparados
        require __DIR__ . '/../views/employees/index.php';
    }

    /**
     * Muestra la ficha completa de un empleado, incluyendo datos relacionados.
     */
    public function show() {
        $this->authMiddleware->requireAuth();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->redirectWithError('ID de empleado inválido.');
        }

        // Carga el empleado y sus datos relacionados
        $employee = $this->employeeModel->findById($id);
        if (!$employee) {
            $this->redirectWithError('Empleado no encontrado.');
        }
        $attachments = $this->attachmentModel->findByEmployee($id);
        $attendance = $this->attendanceModel->findByEmployee($id);
        
        require __DIR__ . '/../views/employees/show.php';
    }

    /**
     * Muestra el formulario para crear un nuevo empleado.
     */
    public function create() {
        $this->authMiddleware->requireAuth();
        $this->authMiddleware->requireAnyRole(['super_admin', 'manager']);
        require __DIR__ . '/../views/employees/create.php';
    }

    /**
     * Procesa los datos del formulario de creación de un nuevo empleado.
     */
    public function store() {
        $this->authMiddleware->requireAuth();
        $this->authMiddleware->requireAnyRole(['super_admin', 'manager']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // MEJORA: Implementar validación de datos aquí
            $errors = $this->validateEmployeeData($_POST);
            if (!empty($errors)) {
                // Manejar errores (ej. volver al formulario con mensajes)
                $_SESSION['form_data'] = $_POST;
                $_SESSION['form_errors'] = $errors;
                header('Location: index.php?action=employees_create');
                exit;
            }

            if ($this->employeeModel->create($_POST)) {
                $this->redirectWithSuccess('Empleado creado exitosamente.');
            } else {
                $this->redirectWithError('No se pudo crear el empleado. Verifique que el documento no esté duplicado.');
            }
        }
    }
    
    /**
     * Muestra el formulario para editar un empleado existente.
     */
    public function edit() {
        $this->authMiddleware->requireAuth();
        $this->authMiddleware->requireAnyRole(['super_admin', 'manager']);
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            $this->redirectWithError('ID de empleado inválido.');
        }

        $employee = $this->employeeModel->findById($id);
        if (!$employee) {
            $this->redirectWithError('Empleado no encontrado.');
        }

        require __DIR__ . '/../views/employees/edit.php';
    }

    /**
     * Procesa los datos del formulario de edición.
     */
    public function update() {
        $this->authMiddleware->requireAuth();
        $this->authMiddleware->requireAnyRole(['super_admin', 'manager']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                $this->redirectWithError('ID de empleado inválido.');
            }
            
            // MEJORA: Implementar validación aquí también
            
            if ($this->employeeModel->update($id, $_POST)) {
                $this->redirectWithSuccess('Empleado actualizado exitosamente.', 'employees_show&id=' . $id);
            } else {
                $this->redirectWithError('No se pudo actualizar el empleado.', 'employees_edit&id=' . $id);
            }
        }
    }

    /**
     * Elimina un empleado.
     */
    public function delete() {
        $this->authMiddleware->requireAuth();
        $this->authMiddleware->requireRole('super_admin'); // Solo super admin puede borrar
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->redirectWithError('ID de empleado inválido.');
        }

        if ($this->employeeModel->delete($id)) {
            $this->redirectWithSuccess('Empleado eliminado exitosamente.');
        } else {
            $this->redirectWithError('No se pudo eliminar el empleado.');
        }
    }

    // --- MÉTODOS AUXILIARES ---

    /**
     * MEJORA: Validación de datos del empleado.
     * Retorna un array de errores. Si está vacío, los datos son válidos.
     */
    private function validateEmployeeData($data) {
        $errors = [];
        if (empty(trim($data['full_name']))) {
            $errors['full_name'] = 'El nombre completo es obligatorio.';
        }
        if (empty(trim($data['document_number']))) {
            $errors['document_number'] = 'El número de documento es obligatorio.';
        }
        // ... añadir más validaciones aquí (formato de fecha, email, etc.)
        return $errors;
    }

    /** MEJORA: Métodos para manejar mensajes flash y redirecciones */
    private function redirectWithSuccess($message, $action = 'employees_index') {
        $_SESSION['flash_message'] = ['type' => 'success', 'text' => $message];
        header("Location: index.php?action=$action");
        exit();
    }

    private function redirectWithError($message, $action = 'employees_index') {
        $_SESSION['flash_message'] = ['type' => 'error', 'text' => $message];
        header("Location: index.php?action=$action");
        exit();
    }
}