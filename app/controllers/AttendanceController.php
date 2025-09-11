<?php
require_once __DIR__ . '/../models/AttendanceModel.php';
require_once __DIR__ . '/../models/EmployeeModel.php'; // Necesitaremos el modelo de empleado

class AttendanceController {
    public function registerFromFingerprint() {
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->fingerprint)) {
            http_response_code(400);
            echo json_encode(["message" => "Dato de huella es requerido."]);
            return;
        }

     // En AttendanceController.php
// ...
try {
    // 1. Instanciamos el modelo Employee (que ahora tiene el nuevo método)
    $employeeModel = new Employee(); // Usando tu nombre de clase

    // 2. Llamamos al nuevo método para encontrar al empleado
    $employee = $employeeModel->findEmployeeByFingerprint($data->fingerprint);

    if (!$employee) {
        http_response_code(404);
        echo json_encode(["message" => "Empleado no reconocido."]);
        return;
    }

    // 3. Si lo encontramos, procedemos a registrar la asistencia
    $attendanceModel = new AttendanceModel();
    $result = $attendanceModel->createOrUpdateRecord($employee['id']);
    
    http_response_code(201);
    echo json_encode([
        "message" => "Bienvenido/a " . $employee['full_name'],
        "details" => $result
    ]);

} catch (Exception $e) {
    // ...
}
//...
?>