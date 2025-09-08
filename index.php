<?php
require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/controllers/AuthController.php';

// Manejo de rutas básico
$action = $_GET['action'] ?? '';
$authController = new AuthController();
$authMiddleware = new AuthMiddleware();

switch ($action) {
    case 'login':
        $authController->login();
        break;
    case 'logout':
        $authController->logout();
        break;
    default:
        // Verificar autenticación para rutas protegidas
        if (strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false) {
            $authMiddleware->requireAuth();
        }
        break;
}
?>