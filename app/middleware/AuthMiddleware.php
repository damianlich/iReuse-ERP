<?php
require_once '../controllers/AuthController.php';

class AuthMiddleware {
    private $authController;

    public function __construct() {
        $this->authController = new AuthController();
    }

    public function requireAuth() {
        $this->authController->requireAuth();
    }

    public function requireRole($requiredRole) {
        $this->requireAuth();
        
        if ($_SESSION['role'] !== $requiredRole) {
            $this->handleUnauthorized();
        }
    }

    public function requireAnyRole($allowedRoles) {
        $this->requireAuth();
        
        if (!in_array($_SESSION['role'], $allowedRoles)) {
            $this->handleUnauthorized();
        }
    }

    private function handleUnauthorized() {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Acceso no autorizado para su rol de usuario'
        ]);
        exit();
    }

    public function getCurrentUser() {
        $this->requireAuth();
        
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role'],
            'employee_id' => $_SESSION['employee_id']
        ];
    }
}
?>