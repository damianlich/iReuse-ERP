<!-- Contenido para la pestaña de Información -->
<h5 class="mb-3 border-bottom pb-2">Datos Personales</h5>
<div class="row">
    <div class="col-md-6">
        <p><span class="info-label">Nombre Completo:</span> <span class="info-value"><?php echo htmlspecialchars($employee['full_name']); ?></span></p>
        <p><span class="info-label">Documento:</span> <span class="info-value"><?php echo htmlspecialchars($employee['document_type'] . ' ' . $employee['document_number']); ?></span></p>
        <p><span class="info-label">Fecha de Nacimiento:</span> <span class="info-value"><?php echo date("d/m/Y", strtotime($employee['birth_date'])); ?></span></p>
    </div>
    <div class="col-md-6">
        <p><span class="info-label">Estado Civil:</span> <span class="info-value"><?php echo htmlspecialchars($employee['marital_status']); ?></span></p>
        <p><span class="info-label">Personas a Cargo:</span> <span class="info-value"><?php echo htmlspecialchars($employee['dependents']); ?></span></p>
        <p><span class="info-label">Nivel Educativo:</span> <span class="info-value"><?php echo htmlspecialchars($employee['education_level']); ?></span></p>
    </div>
</div>

<h5 class="mt-4 mb-3 border-bottom pb-2">Información Laboral</h5>
<div class="row">
    <div class="col-md-6">
        <p><span class="info-label">Cargo Actual:</span> <span class="info-value"><?php echo htmlspecialchars($employee['current_position']); ?></span></p>
        <p><span class="info-label">Departamento:</span> <span class="info-value"><?php echo htmlspecialchars($employee['department']); ?></span></p>
        <p><span class="info-label">Jefe Inmediato:</span> <span class="info-value"><?php echo htmlspecialchars($employee['immediate_boss']); ?></span></p>
    </div>
    <div class="col-md-6">
        <p><span class="info-label">Fecha de Contratación:</span> <span class="info-value"><?php echo date("d/m/Y", strtotime($employee['hire_date'])); ?></span></p>
        <p><span class="info-label">Tipo de Contrato:</span> <span class="info-value"><?php echo htmlspecialchars($employee['contract_type']); ?></span></p>
        <p><span class="info-label">Vacaciones Pendientes:</span> <span class="info-value"><?php echo htmlspecialchars($employee['pending_vacations']); ?> días</span></p>
    </div>
</div>

<h5 class="mt-4 mb-3 border-bottom pb-2">Seguridad Social</h5>
<div class="row">
    <div class="col-md-4"><p><span class="info-label">EPS:</span> <span class="info-value"><?php echo htmlspecialchars($employee['eps']); ?></span></p></div>
    <div class="col-md-4"><p><span class="info-label">AFP:</span> <span class="info-value"><?php echo htmlspecialchars($employee['afp']); ?></span></p></div>
    <div class="col-md-4"><p><span class="info-label">ARL:</span> <span class="info-value"><?php echo htmlspecialchars($employee['arl']); ?></span></p></div>
</div>