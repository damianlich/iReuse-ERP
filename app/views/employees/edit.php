<?php
// Middleware y carga de datos (asumido desde el controlador)
require_once '../../app/middleware/AuthMiddleware.php';
$authMiddleware = new AuthMiddleware();
$currentUser = $authMiddleware->getCurrentUser();

if (!isset($employee)) {
    header('Location: index.php?action=employees_index&error=employee_not_found');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empleado - <?php echo htmlspecialchars($employee['full_name']); ?></title>
    <!-- Estilos de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- ✅ Hoja de estilos específica -->
    <link rel="stylesheet" href="../../public/assets/css/employees.css">
</head>
<body>
    <?php include '../partials/header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include '../partials/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Editar Empleado</h1>
                    <a href="index.php?action=employees_index" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <!-- ✅ El formulario tiene el ID 'employee-form' y apunta a la acción de actualizar -->
                        <form id="employee-form" method="POST" action="index.php?action=employees_update&id=<?php echo htmlspecialchars($employee['id']); ?>">
                            
                            <!-- Reutilizamos el mismo parcial. La lógica PHP dentro del parcial se
                                 encargará de rellenar los valores porque $employee está definido. -->
                            <?php include 'partials/form_fields.php'; ?>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle-fill"></i> Actualizar Cambios
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
    <!-- ✅ Script de validación específico -->
    <script src="../../public/assets/js/employees.js"></script>
</body>
</html>