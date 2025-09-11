<?php
// Establecer cabeceras para permitir peticiones y devolver JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Incluir los archivos necesarios
require_once __DIR__ . '/../../../app/controllers/AttendanceController.php';

// Verificar que el método de la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
    exit();
}

// Instanciar el controlador y llamar al método que maneja el registro
$controller = new AttendanceController();
$controller->handleRegistrationRequest();

?>