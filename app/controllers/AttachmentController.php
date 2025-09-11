<?php
// Requerir los modelos que vamos a utilizar
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/AttendanceModel.php';

class AttendanceController {

    /**
     * Maneja la solicitud completa de registro de asistencia por huella.
     */
    public function handleRegistrationRequest() {
        try {
            // 1. Obtener los datos JSON de la petición
            $data = json_decode(file_get_contents("php://input"));

            // 2. Validar que la huella fue enviada
            if (empty($data->fingerprint)) {
                http_response_code(400); // Bad Request
                echo json_encode(["success" => false, "message" => "No se recibieron datos de la huella."]);
                return;
            }

            // 3. Identificar al empleado usando el modelo Employee
            $employeeModel = new Employee();
            $employee = $employeeModel->findEmployeeByFingerprint($data->fingerprint);

            // 4. Si no se encuentra el empleado, devolver error 404
            if (!$employee) {
                http_response_code(404); // Not Found
                echo json_encode(["success" => false, "message" => "Empleado no reconocido."]);
                return;
            }

            // 5. Si se encuentra, registrar la asistencia usando AttendanceModel
            $attendanceModel = new AttendanceModel();
            $registrationResult = $attendanceModel->createOrUpdateRecord($employee['id']);

            if (!$registrationResult) {
                 http_response_code(500); // Internal Server Error
                 echo json_encode(["success" => false, "message" => "No se pudo guardar el registro de asistencia."]);
                 return;
            }

            // 6. Construir la respuesta JSON exitosa que espera el frontend
            $this->sendSuccessResponse($employee, $registrationResult);

        } catch (Exception $e) {
            // Manejo de errores inesperados
            http_response_code(500); // Internal Server Error
            echo json_encode([
                "success" => false,
                "message" => "Ocurrió un error interno en el servidor: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Construye y envía la respuesta JSON en caso de éxito.
     * @param array $employee - Datos del empleado (id, full_name, etc.)
     * @param array $registrationResult - Resultado del modelo de asistencia (action, id)
     */
    private function sendSuccessResponse($employee, $registrationResult) {
        // NOTA: Tu tabla 'employees' no tiene un campo para la foto.
        // Aquí simulamos una URL. Deberías agregar un campo 'photo_url' a tu tabla.
        $photo_url = "assets/uploads/photos/" . $employee['id'] . ".jpg";
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $photo_url)) {
            $photo_url = "assets/img/default-user.png"; // Imagen por defecto si no existe
        }


        $response = [
            "success" => true,
            "message" => "Registro exitoso para " . $employee['full_name'],
            "employee" => [
                "id" => (int)$employee['id'],
                "name" => $employee['full_name'],
                "photo_url" => $photo_url 
            ],
            "details" => $registrationResult
        ];
        
        http_response_code(200); // OK
        echo json_encode($response);
    }
}