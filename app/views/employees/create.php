<?php
// Requiere el middleware para verificar la sesión del usuario.
require_once '../../app/middleware/AuthMiddleware.php';
$authMiddleware = new AuthMiddleware();
$currentUser = $authMiddleware->getCurrentUser(); // Obtiene datos del usuario o redirige si no está logueado.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Empleado - Sistema de Gestión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include '../partials/header.php'; // Incluye la cabecera común ?>

    <div class="container-fluid">
        <div class="row">
            <?php include '../partials/sidebar.php'; // Incluye la barra lateral de navegación ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Registrar Nuevo Empleado</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="index.php?action=employees_index" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <!-- El formulario apunta al controlador frontal con la acción específica -->
                        <form method="POST" action="index.php?action=employees_store">
                            <?php include 'partials/form_fields.php'; // Incluye los campos del formulario ?>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Guardar Empleado
                                    </button>
                                    <a href="index.php?action=employees_index" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/employees.js"></script>
</body>
</html>