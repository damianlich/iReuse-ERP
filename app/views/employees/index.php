<?php
// Middleware para asegurar que el usuario está autenticado
require_once '../../app/middleware/AuthMiddleware.php';
$authMiddleware = new AuthMiddleware();
$currentUser = $authMiddleware->getCurrentUser();

// ASUNCIÓN CLAVE: El controlador que llama a esta vista ya ha preparado los datos.
// Ejemplo de variables que el controlador debería definir:
// $employees = [ [...], [...] ]; // Un array con los datos de los empleados
// $pagination = [
//     'current_page' => 1,
//     'total_pages' => 5,
// ];
// $search_keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';

// Para facilitar la lectura en la vista, extraemos las variables de paginación
$current_page = $pagination['current_page'];
$total_pages = $pagination['total_pages'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empleados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../../public/assets/css/employees.css" rel="stylesheet">
</head>
<body>
    <?php include '../partials/header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include '../partials/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Gestión de Empleados</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="index.php?action=employees_create" class="btn btn-primary">
                            <i class="bi bi-person-plus-fill"></i> Nuevo Empleado
                        </a>
                    </div>
                </div>

                <?php
                // Sistema de mensajes Flash (éxito/error) mejorado
                if (isset($_SESSION['flash_message'])) {
                    $message = $_SESSION['flash_message'];
                    unset($_SESSION['flash_message']);
                    $alert_type = $message['type'] === 'success' ? 'alert-success' : 'alert-danger';
                    echo "<div class='alert {$alert_type} alert-dismissible fade show' role='alert'>{$message['text']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
                }
                ?>

                <!-- Formulario de Búsqueda -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="index.php">
                            <input type="hidden" name="action" value="employees_index">
                            <div class="input-group">
                                <input type="text" class="form-control" name="keywords" placeholder="Buscar por nombre, documento, cargo..." value="<?php echo htmlspecialchars($search_keywords); ?>">
                                <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i> Buscar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabla de Empleados -->
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Lista de Empleados</h5></div>
                    <div class="card-body">
                        <?php if (!empty($employees)) { ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Nombre Completo</th>
                                            <th>Documento</th>
                                            <th>Cargo</th>
                                            <th>Departamento</th>
                                            <th>Fecha Ingreso</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($employees as $employee) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($employee['full_name']); ?></td>
                                                <td><?php echo htmlspecialchars($employee['document_type'] . ' ' . $employee['document_number']); ?></td>
                                                <td><?php echo htmlspecialchars($employee['current_position']); ?></td>
                                                <td><?php echo htmlspecialchars($employee['department']); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($employee['hire_date'])); ?></td>
                                                <td class="text-center">
                                                    <a href="index.php?action=employees_show&id=<?php echo $employee['id']; ?>" class="btn btn-sm btn-info" title="Ver Ficha"><i class="bi bi-eye"></i></a>
                                                    <a href="index.php?action=employees_edit&id=<?php echo $employee['id']; ?>" class="btn btn-sm btn-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                                                    <?php if ($currentUser['role'] === 'super_admin') { ?>
                                                        <a href="index.php?action=employees_delete&id=<?php echo $employee['id']; ?>" class="btn btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Está seguro de que desea eliminar a este empleado? Esta acción no se puede deshacer.')"><i class="bi bi-trash"></i></a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginación Robusta (conserva la búsqueda) -->
                            <?php if ($total_pages > 1) {
                                // Construir la URL base para los enlaces de paginación
                                $query_params = ['action' => 'employees_index'];
                                if (!empty($search_keywords)) {
                                    $query_params['keywords'] = $search_keywords;
                                }
                            ?>
                                <nav>
                                    <ul class="pagination justify-content-center mt-4">
                                        <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?<?php echo http_build_query(array_merge($query_params, ['page' => $current_page - 1])); ?>">Anterior</a>
                                        </li>
                                        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                            <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                                <a class="page-link" href="?<?php echo http_build_query(array_merge($query_params, ['page' => $i])); ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php } ?>
                                        <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?<?php echo http_build_query(array_merge($query_params, ['page' => $current_page + 1])); ?>">Siguiente</a>
                                        </li>
                                    </ul>
                                </nav>
                            <?php } ?>

                        <?php } else { ?>
                            <!-- Mensaje contextual si no hay resultados -->
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle-fill"></i>
                                <?php if (!empty($search_keywords)) {
                                    echo "No se encontraron empleados que coincidan con la búsqueda: <strong>\"" . htmlspecialchars($search_keywords) . "\"</strong>.";
                                } else {
                                    echo "Aún no hay empleados registrados. ¡Añade el primero!";
                                } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/employees.js"></script>
</body>
</html>```