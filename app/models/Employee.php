<?php
require_once __DIR__ . '/../../config/database.php';

class Employee {
    private $conn;
    private $table_name = "employees";

    // MEJORA: El modelo ahora gestiona su propia conexión a la BD.
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * MEJORA: Busca un empleado por su ID y devuelve sus datos como un array.
     * @param int $id El ID del empleado.
     * @return array|false Un array asociativo con los datos del empleado o false si no se encuentra.
     */
    public function findById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * MEJORA: Obtiene una lista paginada de todos los empleados.
     * Devuelve un array de empleados.
     */
    public function readAll($page, $records_per_page) {
        $offset = ($page - 1) * $records_per_page;
        $query = "SELECT id, full_name, document_type, document_number, current_position, department, hire_date 
                  FROM " . $this->table_name . " 
                  ORDER BY full_name ASC 
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $records_per_page, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * MEJORA: Busca empleados por palabras clave de forma paginada.
     * Devuelve un array de empleados que coinciden.
     */
    public function search($keywords, $page, $records_per_page) {
        $offset = ($page - 1) * $records_per_page;
        $query = "SELECT id, full_name, document_type, document_number, current_position, department, hire_date 
                  FROM " . $this->table_name . " 
                  WHERE full_name LIKE :keywords OR document_number LIKE :keywords 
                  OR current_position LIKE :keywords OR department LIKE :keywords 
                  ORDER BY full_name ASC 
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $search_term = "%{$keywords}%";
        $stmt->bindParam(':keywords', $search_term);
        $stmt->bindParam(':limit', $records_per_page, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// ... (Después del método search() y antes de create())

    /**
     * NUEVO: Busca un empleado comparando la huella digital recibida con las almacenadas.
     * @param string $receivedFingerprintData Los datos de la huella capturada (probablemente en Base64).
     * @return array|false Un array asociativo con los datos del empleado si se encuentra, o false si no hay coincidencia.
     */
    public function findEmployeeByFingerprint($receivedFingerprintData) {
        // 1. Obtenemos las plantillas de huellas de todos los empleados que tengan una registrada.
        // Esto es más eficiente que traer a todos los empleados.
        $query = "SELECT id, full_name, fingerprint_template FROM " . $this->table_name . " WHERE fingerprint_template IS NOT NULL AND fingerprint_template != ''";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $employeesWithTemplates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Manejar error de base de datos
            error_log("Error al obtener plantillas de huellas: " . $e->getMessage());
            return false;
        }

        // 2. Iteramos sobre los resultados y comparamos cada plantilla.
        // Esta es la parte que requiere una LIBRERÍA DE TERCEROS.
        
        // --- INICIO DE PSEUDOCÓDIGO (REQUIERE LIBRERÍA EXTERNA) ---

        // Suponiendo que has incluido una librería de comparación, el código se vería así:
        // require_once 'path/to/fingerprint_matching_library.php';
        // $matcher = new FingerprintMatcher();
        // $matcher->setConfidenceThreshold(90); // Establecer un umbral de confianza

        foreach ($employeesWithTemplates as $employee) {
            $storedTemplate = $employee['fingerprint_template'];
            
            // La función match() de la librería compararía las dos plantillas
            // y devolvería true si coinciden con suficiente confianza.
            // if ($matcher->match($receivedFingerprintData, $storedTemplate)) {
            //     // ¡Coincidencia encontrada! Devolvemos los datos del empleado.
            //     // Solo necesitamos el ID y el nombre para el mensaje de bienvenida.
            //     return [
            //         'id' => $employee['id'],
            //         'full_name' => $employee['full_name']
            //     ];
            // }
        }
        // --- FIN DE PSEUDOCÓDIGO ---

        // Si el bucle termina sin encontrar coincidencias, devolvemos false.
        return false;
    }

    /**
     * MEJORA: Crea un nuevo empleado a partir de un array de datos.
     * ... (el resto de tu código sigue igual)
     */

    /**
     * MEJORA: Crea un nuevo empleado a partir de un array de datos.
     * @param array $data Un array asociativo con los datos del empleado.
     * @return int|false El ID del nuevo empleado o false en caso de error.
     */
    public function create($data) {
        // MEJORA: Consulta explícita para mayor seguridad y claridad.
        $query = "INSERT INTO " . $this->table_name . " (
                    full_name, document_type, document_number, birth_date, marital_status, dependents, 
                    education_level, work_experience, current_position, department, immediate_boss, 
                    hire_date, contract_type, eps, afp, arl, licenses_certifications, 
                    performance_observations, bank_account, bank_name, salary, payment_frequency, 
                    allowances, work_schedule, pending_vacations
                  ) VALUES (
                    :full_name, :document_type, :document_number, :birth_date, :marital_status, :dependents, 
                    :education_level, :work_experience, :current_position, :department, :immediate_boss, 
                    :hire_date, :contract_type, :eps, :afp, :arl, :licenses_certifications, 
                    :performance_observations, :bank_account, :bank_name, :salary, :payment_frequency, 
                    :allowances, :work_schedule, :pending_vacations
                  )";
        $stmt = $this->conn->prepare($query);
        $this->bindValues($stmt, $data); // MEJORA: Usar método auxiliar

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * MEJORA: Actualiza un empleado a partir de un ID y un array de datos.
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET
                    full_name=:full_name, document_type=:document_type, document_number=:document_number, 
                    birth_date=:birth_date, marital_status=:marital_status, dependents=:dependents, 
                    education_level=:education_level, work_experience=:work_experience, current_position=:current_position, 
                    department=:department, immediate_boss=:immediate_boss, hire_date=:hire_date, 
                    contract_type=:contract_type, eps=:eps, afp=:afp, arl=:arl, 
                    licenses_certifications=:licenses_certifications, performance_observations=:performance_observations, 
                    bank_account=:bank_account, bank_name=:bank_name, salary=:salary, 
                    payment_frequency=:payment_frequency, allowances=:allowances, work_schedule=:work_schedule, 
                    pending_vacations=:pending_vacations
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->bindValues($stmt, $data); // MEJORA: Reutilizar el método auxiliar
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
    
    /**
     * MEJORA: Elimina un empleado por su ID.
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // --- Métodos de Conteo para Paginación ---

    public function countAll() {
        $query = "SELECT COUNT(*) FROM " . $this->table_name;
        return (int) $this->conn->query($query)->fetchColumn();
    }

    public function countSearchResults($keywords) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . "
                  WHERE full_name LIKE :keywords OR document_number LIKE :keywords 
                  OR current_position LIKE :keywords OR department LIKE :keywords";
        $stmt = $this->conn->prepare($query);
        $search_term = "%{$keywords}%";
        $stmt->bindParam(':keywords', $search_term);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
    
    /**
     * MEJORA: Método auxiliar privado para enlazar valores y evitar repetición de código (DRY).
     */
    private function bindValues($stmt, $data) {
        // Limpia y enlaza los valores de forma segura.
        $stmt->bindValue(':full_name', !empty($data['full_name']) ? htmlspecialchars(strip_tags($data['full_name'])) : null);
        $stmt->bindValue(':document_type', !empty($data['document_type']) ? $data['document_type'] : null);
        $stmt->bindValue(':document_number', !empty($data['document_number']) ? htmlspecialchars(strip_tags($data['document_number'])) : null);
        $stmt->bindValue(':birth_date', !empty($data['birth_date']) ? $data['birth_date'] : null);
        $stmt->bindValue(':marital_status', !empty($data['marital_status']) ? $data['marital_status'] : null);
        $stmt->bindValue(':dependents', isset($data['dependents']) ? (int)$data['dependents'] : 0, PDO::PARAM_INT);
        $stmt->bindValue(':education_level', !empty($data['education_level']) ? htmlspecialchars(strip_tags($data['education_level'])) : null);
        $stmt->bindValue(':work_experience', !empty($data['work_experience']) ? htmlspecialchars(strip_tags($data['work_experience'])) : null);
        $stmt->bindValue(':current_position', !empty($data['current_position']) ? htmlspecialchars(strip_tags($data['current_position'])) : null);
        $stmt->bindValue(':department', !empty($data['department']) ? htmlspecialchars(strip_tags($data['department'])) : null);
        $stmt->bindValue(':immediate_boss', !empty($data['immediate_boss']) ? htmlspecialchars(strip_tags($data['immediate_boss'])) : null);
        $stmt->bindValue(':hire_date', !empty($data['hire_date']) ? $data['hire_date'] : null);
        $stmt->bindValue(':contract_type', !empty($data['contract_type']) ? $data['contract_type'] : null);
        $stmt->bindValue(':eps', !empty($data['eps']) ? htmlspecialchars(strip_tags($data['eps'])) : null);
        $stmt->bindValue(':afp', !empty($data['afp']) ? htmlspecialchars(strip_tags($data['afp'])) : null);
        $stmt->bindValue(':arl', !empty($data['arl']) ? htmlspecialchars(strip_tags($data['arl'])) : null);
        $stmt->bindValue(':licenses_certifications', !empty($data['licenses_certifications']) ? htmlspecialchars(strip_tags($data['licenses_certifications'])) : null);
        $stmt->bindValue(':performance_observations', !empty($data['performance_observations']) ? htmlspecialchars(strip_tags($data['performance_observations'])) : null);
        $stmt->bindValue(':bank_account', !empty($data['bank_account']) ? htmlspecialchars(strip_tags($data['bank_account'])) : null);
        $stmt->bindValue(':bank_name', !empty($data['bank_name']) ? htmlspecialchars(strip_tags($data['bank_name'])) : null);
        $stmt->bindValue(':salary', !empty($data['salary']) ? (float)$data['salary'] : null);
        $stmt->bindValue(':payment_frequency', !empty($data['payment_frequency']) ? $data['payment_frequency'] : null);
        $stmt->bindValue(':allowances', !empty($data['allowances']) ? (float)$data['allowances'] : 0);
        $stmt->bindValue(':work_schedule', !empty($data['work_schedule']) ? htmlspecialchars(strip_tags($data['work_schedule'])) : null);
        $stmt->bindValue(':pending_vacations', isset($data['pending_vacations']) ? (int)$data['pending_vacations'] : 0, PDO::PARAM_INT);
    }
}