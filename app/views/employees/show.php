<?php
// 1. Middleware y obtención de datos
// -----------------------------------------------------------------------------
require_once '../../app/middleware/AuthMiddleware.php';
$authMiddleware = new AuthMiddleware();
$currentUser = $authMiddleware->getCurrentUser();

// Asumimos que el controlador ha cargado los datos del empleado, sus documentos y su asistencia.
// Ejemplo de cómo el controlador pasaría los datos:
// $employee = $employeeModel->find(1);
// $attachments = $attachmentModel->findByEmployee(1);
// $attendance = $attendanceModel->findByEmployee(1);

// Si el empleado no existe, redirigir.
if (!isset($employee)) {
    header('Location: index.php?action=employees_index&error=not_found');
    exit();
}

// 2. Lógica de control de acceso por roles
// -----------------------------------------------------------------------------
$canViewPayroll = in_array($currentUser['role'], ['super_admin', 'manager']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha del Empleado - <?php echo htmlspecialchars($employee['full_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Estilo para mejorar la presentación de los datos */
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .info-value {
            color: #333;
        }
    </style>
</head>
<body>
    <?php include '../partials/header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include '../partials/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Ficha de Empleado</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                         <a href="index.php?action=employees_edit&id=<?php echo $employee['id']; ?>" class="btn btn-sm btn-outline-primary me-2">
                            <i class="bi bi-pencil-square"></i> Editar Empleado
                        </a>
                        <a href="index.php?action=employees_index" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver al Listado
                        </a>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h4 class="mb-0">
                            <i class="bi bi-person-badge"></i>
                            <?php echo htmlspecialchars($employee['full_name']); ?>
                        </h4>
                        <small class="text-muted"><?php echo htmlspecialchars($employee['current_position']); ?></small>
                    </div>
                    <div class="card-body">
                        <!-- Sistema de Pestañas -->
                        <ul class="nav nav-tabs" id="employeeTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true"><i class="bi bi-info-circle"></i> Información</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs" type="button" role="tab" aria-controls="docs" aria-selected="false"><i class="bi bi-folder2-open"></i> Documentos</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab" aria-controls="attendance" aria-selected="false"><i class="bi bi-calendar-check"></i> Asistencia</button>
                            </li>
                            <?php if ($canViewPayroll): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="payroll-tab" data-bs-toggle="tab" data-bs-target="#payroll" type="button" role="tab" aria-controls="payroll" aria-selected="false"><i class="bi bi-cash-coin"></i> Nómina</button>
                            </li>
                            <?php endif; ?>
                        </ul>

                        <!-- Contenido de las Pestañas -->
                        <div class="tab-content pt-3" id="employeeTabContent">
                            <!-- Pestaña 1: Información Personal y Laboral -->
                            <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                                <?php include 'partials/show_info.php'; ?>
                            </div>

                            <!-- Pestaña 2: Documentos Anexos -->
                            <div class="tab-pane fade" id="docs" role="tabpanel" aria-labelledby="docs-tab">
                                <?php include 'partials/show_attachments.php'; ?>
                            </div>

                            <!-- Pestaña 3: Registro de Asistencia -->
                            <div class="tab-pane fade" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
                                <?php include 'partials/show_attendance.php'; ?>
                            </div>

                            <!-- Pestaña 4: Información de Nómina (con control de acceso) -->
                            <?php if ($canViewPayroll): ?>
                            <div class="tab-pane fade" id="payroll" role="tabpanel" aria-labelledby="payroll-tab">
                                <?php include 'partials/show_payroll.php'; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>