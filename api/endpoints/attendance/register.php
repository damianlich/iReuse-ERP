<?php
// ... (cargas de configuración, etc.)
require_once __DIR__ . '/../../../app/controllers/AttendanceController.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

$controller = new AttendanceController();
$controller->registerFromFingerprint();
?>