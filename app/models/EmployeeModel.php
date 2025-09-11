<?php
class EmployeeModel {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    /**
     * Busca un empleado comparando la huella recibida con las almacenadas.
     * ¡¡ESTA ES LA PARTE MÁS COMPLEJA!!
     */
    public function findEmployeeByFingerprint($receivedFingerprintData) {
        // 1. Obtener TODAS las plantillas de huellas de la base de datos.
        $query = "SELECT id, full_name, fingerprint_template FROM employees WHERE fingerprint_template IS NOT NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $allEmployees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Comparar la huella recibida con cada una de las almacenadas.
        // PHP no tiene funciones nativas para comparar plantillas de huellas.
        // NECESITARÁS UNA LIBRERÍA DE TERCEROS EN EL SERVIDOR que sea compatible
        // con el formato de plantilla de tu lector (ej. ISO/IEC 19794-2).
        
        // --- INICIO DE PSEUDOCÓDIGO (REQUIERE LIBRERÍA EXTERNA) ---
        // include 'path/to/fingerprint_matching_library.php';
        // $matcher = new FingerprintMatcher();

        foreach ($allEmployees as $employee) {
            $storedTemplate = $employee['fingerprint_template'];
            
            // La función match() de la librería retornaría true si hay coincidencia
            // if ($matcher->match($receivedFingerprintData, $storedTemplate)) {
            //     return $employee; // ¡Encontrado! Retornamos los datos del empleado.
            // }
        }
        // --- FIN DE PSEUDOCÓDIGO ---

        // Simulación para propósitos de demostración:
        // En un caso real, la comparación es binaria y compleja.
        // Aquí simulamos que encontramos a un empleado si el string es idéntico.
        foreach ($allEmployees as $employee) {
            if ($employee['fingerprint_template'] === $receivedFingerprintData) {
                return $employee;
            }
        }

        return null; // Si no se encuentra ninguna coincidencia
    }
}
?>