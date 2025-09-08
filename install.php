<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Crear tabla de usuarios
    $query = "CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(100) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        employee_id INT NULL,
        role ENUM('super_admin', 'supervisor', 'employee') NOT NULL DEFAULT 'employee',
        is_active BOOLEAN DEFAULT TRUE,
        last_login DATETIME NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    $db->exec($query);

    // Insertar usuario admin por defecto
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $query = "INSERT INTO users (username, password_hash, role, is_active) 
              VALUES ('admin', :password, 'super_admin', TRUE)
              ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':password', $password_hash);
    $stmt->execute();

    echo "Base de datos configurada correctamente. Usuario: admin, Contraseña: admin123";

} catch (PDOException $e) {
    echo "Error en la instalación: " . $e->getMessage();
}
?>