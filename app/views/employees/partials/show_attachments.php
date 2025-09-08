<!-- Contenido para la pestaña de Documentos -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-cloud-upload"></i> Subir Nuevo Documento
    </div>
    <div class="card-body">
        <form action="index.php?action=attachments_upload" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="employee_id" value="<?php echo $employee['id']; ?>">
            <div class="row">
                <div class="col-md-5 mb-2">
                    <input type="file" class="form-control" name="document" required>
                </div>
                <div class="col-md-5 mb-2">
                    <input type="text" class="form-control" name="attachment_type" placeholder="Ej: Hoja de Vida, Cédula" required>
                </div>
                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-primary w-100">Subir</button>
                </div>
            </div>
        </form>
    </div>
</div>

<h5 class="mt-4 mb-3">Documentos Existentes</h5>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-light">
            <tr>
                <th>Nombre del Archivo</th>
                <th>Tipo de Documento</th>
                <th>Fecha de Subida</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($attachments)): ?>
                <?php foreach ($attachments as $file): ?>
                <tr>
                    <td><?php echo htmlspecialchars($file['file_name']); ?></td>
                    <td><?php echo htmlspecialchars($file['attachment_type']); ?></td>
                    <td><?php echo date("d/m/Y h:i A", strtotime($file['upload_date'])); ?></td>
                    <td>
                        <a href="<?php echo htmlspecialchars($file['file_path']); ?>" class="btn btn-sm btn-info" target="_blank" title="Ver/Descargar">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">No hay documentos adjuntos.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>