<?php
/**
 * public/index.php - Controlador Frontal Único
 */

// 1. INICIALIZACIÓN
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../config/database.php';

// 2. ENRUTAMIENTO
$action = $_GET['action'] ?? 'login'; // Cambiamos la acción por defecto a 'login'

// 3. DESPACHO DE CONTROLADORES
switch ($action) {
    // --- Rutas de Autenticación ---
    case 'login':
        require_once '../app/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->showLoginForm(); // Suponiendo que hay un método para mostrar el form
        break;
        
    case 'authenticate': // Acción para procesar el envío del formulario de login
        require_once '../app/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->authenticate();
        break;

    case 'logout':
        require_once '../app/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    // --- Rutas del Módulo de Empleados ---
    case 'employees_index':
        require_once '../app/controllers/EmployeeController.php';
        $controller = new EmployeeController();
        $controller->index();
        break;
    
    // ... (aquí van todas las otras rutas de employees: show, create, store, edit, update, delete) ...
    // Ejemplo:
    case 'employees_show':
        require_once '../app/controllers/EmployeeController.php';
        $controller = new EmployeeController();
        $controller->show();
        break;
        
    // --- Rutas del Módulo de Anexos ---
    case 'attachments_upload':
        require_once '../app/controllers/AttachmentController.php';
        $controller = new AttachmentController();
        $controller->upload();
        break;

    // --- Ruta por Defecto ---
    default:
        // Si el usuario está logueado, llévalo al dashboard. Si no, al login.
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?action=employees_index');
        } else {
            header('Location: index.php?action=login');
        }
        break;
}