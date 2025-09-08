<?php
session_start();
require_once '../models/User.php';
require_once '../config/database.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->userModel = new User($db);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar campos
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            if (empty($username) || empty($password)) {
                $_SESSION['error'] = "Todos los campos son requeridos";
                header("Location: ../views/auth/login.php");
                exit();
            }

            // Buscar usuario
            $stmt = $this->userModel->findByUsername($username);
            
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->userModel->id = $row['id'];
                $this->userModel->password_hash = $row['password_hash'];
                $this->userModel->role = $row['role'];
                $this->userModel->employee_id = $row['employee_id'];
                $this->userModel->is_active = $row['is_active'];

                // Verificar contraseña y estado
                if ($this->userModel->verifyPassword($password)) {
                    if ($this->userModel->is_active) {
                        // Iniciar sesión
                        $_SESSION['user_id'] = $this->userModel->id;
                        $_SESSION['username'] = $username;
                        $_SESSION['role'] = $this->userModel->role;
                        $_SESSION['employee_id'] = $this->userModel->employee_id;
                        $_SESSION['last_activity'] = time();

                        // Actualizar último login
                        $this->userModel->updateLastLogin();

                        // Redirigir según rol
                        $this->redirectByRole();
                    } else {
                        $_SESSION['error'] = ERROR_USER_INACTIVE;
                    }
                } else {
                    $_SESSION['error'] = ERROR_INVALID_CREDENTIALS;
                }
            } else {
                $_SESSION['error'] = ERROR_INVALID_CREDENTIALS;
            }

            header("Location: ../views/auth/login.php");
            exit();
        }
    }

    private function redirectByRole() {
        switch ($_SESSION['role']) {
            case ROLE_SUPER_ADMIN:
                header("Location: ../views/dashboard/admin.php");
                break;
            case ROLE_SUPERVISOR:
                header("Location: ../views/dashboard/supervisor.php");
                break;
            case ROLE_EMPLOYEE:
                header("Location: ../views/dashboard/employee.php");
                break;
            default:
                header("Location: ../views/dashboard/index.php");
                break;
        }
        exit();
    }

    public function logout() {
        $_SESSION = array();
        session_destroy();
        header("Location: ../views/auth/login.php");
        exit();
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && 
               (time() - $_SESSION['last_activity'] < SESSION_EXPIRE_TIME);
    }

    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header("Location: ../views/auth/login.php");
            exit();
        }
        // Actualizar tiempo de última actividad
        $_SESSION['last_activity'] = time();
    }
}
?>