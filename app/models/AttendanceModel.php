<?php
class AttendanceModel {
    private $conn;

    public function __construct() {
        // Lógica para conectarse a la BD usando los datos de database.php
        $this->conn = (new Database())->getConnection(); 
    }

    /**
     * Registra una entrada o una salida para un empleado.
     * Valida si ya hay un registro abierto para evitar duplicados.
     */
    public function createOrUpdateRecord($employeeId) {
        $today = date('Y-m-d');

        // 1. Buscar si ya hay un registro de entrada hoy SIN salida
        $query = "SELECT id, entry_time FROM attendance 
                  WHERE employee_id = :employee_id AND record_date = :record_date AND exit_time IS NULL 
                  ORDER BY entry_time DESC LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->bindParam(':record_date', $today);
        $stmt->execute();
        
        $openRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($openRecord) {
            // 2. SI EXISTE: Es un registro de SALIDA. Lo actualizamos.
            $exitTime = date('Y-m-d H:i:s');
            
            // Algoritmo de cálculo de horas
            $entry = new DateTime($openRecord['entry_time']);
            $exit = new DateTime($exitTime);
            $diff = $exit->diff($entry);
            $hours = $diff->h + ($diff->i / 60);

            $updateQuery = "UPDATE attendance SET exit_time = :exit_time, total_hours = :total_hours WHERE id = :id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':exit_time', $exitTime);
            $updateStmt->bindParam(':total_hours', $hours);
            $updateStmt->bindParam(':id', $openRecord['id']);

            if ($updateStmt->execute()) {
                return ["action" => "clock_out", "id" => $openRecord['id']];
            }
        } else {
            // 3. SI NO EXISTE: Es un registro de ENTRADA. Lo creamos.
            $entryTime = date('Y-m-d H:i:s');
            $insertQuery = "INSERT INTO attendance (employee_id, record_date, entry_time) VALUES (:employee_id, :record_date, :entry_time)";
            $insertStmt = $this->conn->prepare($insertQuery);
            $insertStmt->bindParam(':employee_id', $employeeId);
            $insertStmt->bindParam(':record_date', $today);
            $insertStmt->bindParam(':entry_time', $entryTime);

            if ($insertStmt->execute()) {
                return ["action" => "clock_in", "id" => $this->conn->lastInsertId()];
            }
        }
        
        throw new Exception("No se pudo procesar el registro.");
    }
}
?>