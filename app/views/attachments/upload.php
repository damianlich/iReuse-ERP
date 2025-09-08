<?php
require_once '../../app/middleware/AuthMiddleware.php';
$authMiddleware = new AuthMiddleware();
$currentUser = $authMiddleware->getCurrentUser();

// El controlador debe definir $employee_id antes de cargar esta vista
if (!isset($employee_id)) {
    echo "Error: ID de empleado no especificado.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Anexo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include '../partials/header.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <?php include '../partials/sidebar.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Subir Nuevo Documento</h1>
                    <a href="index.php?action=employees_show&id=<?php echo $employee_id; ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a la Ficha
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form action="index.php?action=attachments_upload" method="POST" enctype="multipart/form-data">
                            <!-- ID del empleado, oculto para el usuario -->
                            <input type="hidden" name="employee_id" value="<?php echo htmlspecialchars($employee_id); ?>">

                            <div class="mb-3">
                                <label for="attachment_type" class="form-label">Tipo de Documento</label>
                                <input type="text" class="form-control" id="attachment_type" name="attachment_type" placeholder="Ej: Hoja de Vida, Cédula, Certificado..." required>
                            </div>

                            <div class="mb-3">
                                <label for="document" class="form-label">Seleccionar Archivo</label>
                                <input class="form-control" type="file" id="document" name="document" required>
                                <div class="form-text">Archivos permitidos: PDF, DOCX, DOC, JPG, PNG. Tamaño máximo: 5MB.</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-cloud-upload"></i> Subir Archivo
                            </button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>