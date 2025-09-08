<?php
require_once '../../app/middleware/AuthMiddleware.php';
$authMiddleware = new AuthMiddleware();
$currentUser = $authMiddleware->getCurrentUser();

// Simular datos para el dashboard (esto vendrá de la base de datos)
$stats = [
    'total_employees' => 145,
    'active_employees' => 138,
    'today_attendance' => 127,
    'pending_requests' => 8
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Gestión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-people-fill"></i> iReuse SAS
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="bi bi-person-circle"></i> 
                    <?php echo htmlspecialchars($currentUser['username']); ?>
                    (<?php echo htmlspecialchars($currentUser['role']); ?>)
                </span>
                <a href="../../app/controllers/AuthController.php?action=logout" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-people"></i> Empleados
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-calendar-check"></i> Asistencia
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-cash-coin"></i> Nómina
                            </a>
                        </li>
                        <?php if ($currentUser['role'] === ROLE_SUPER_ADMIN): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-gear"></i> Administración
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                </div>

                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Empleados</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $stats['total_employees']; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-people-fill fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Empleados Activos</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $stats['active_employees']; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-person-check fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Asistencia Hoy</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $stats['today_attendance']; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-calendar-day fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Solicitudes Pendientes</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $stats['pending_requests']; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-clock-history fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header bg-white">
                                <h6 class="m-0 font-weight-bold text-primary">Actividad Reciente</h6>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-person-plus text-success"></i>
                                            Nuevo empleado registrado
                                        </div>
                                        <small class="text-muted">Hace 2 horas</small>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-calendar-event text-info"></i>
                                            Registro de asistencia completado
                                        </div>
                                        <small class="text-muted">Hoy 8:30 AM</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>