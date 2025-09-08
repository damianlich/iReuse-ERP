<?php
// Define una función auxiliar para facilitar la impresión de valores
function oldValue($field_name, $employee_data) {
    return htmlspecialchars($employee_data[$field_name] ?? '');
}

// Define una función auxiliar para seleccionar opciones en dropdowns
function setSelected($field_name, $value, $employee_data) {
    return isset($employee_data[$field_name]) && $employee_data[$field_name] == $value ? 'selected' : '';
}
?>

<div class="row">
    <!-- Datos Personales -->
    <div class="col-md-6 mb-3">
        <label for="full_name" class="form-label">Nombre Completo</label>
        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo oldValue('full_name', $employee ?? []); ?>" required>
    </div>
    <div class="col-md-3 mb-3">
        <label for="document_type" class="form-label">Tipo de Documento</label>
        <select class="form-select" id="document_type" name="document_type" required>
            <option value="CC" <?php echo setSelected('document_type', 'CC', $employee ?? []); ?>>Cédula de Ciudadanía</option>
            <option value="CE" <?php echo setSelected('document_type', 'CE', $employee ?? []); ?>>Cédula de Extranjería</option>
            <option value="TI" <?php echo setSelected('document_type', 'TI', $employee ?? []); ?>>Tarjeta de Identidad</option>
            <option value="PASAPORTE" <?php echo setSelected('document_type', 'PASAPORTE', $employee ?? []); ?>>Pasaporte</option>
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label for="document_number" class="form-label">Número de Documento</label>
        <input type="text" class="form-control" id="document_number" name="document_number" value="<?php echo oldValue('document_number', $employee ?? []); ?>" required>
    </div>
    <div class="col-md-4 mb-3">
        <label for="birth_date" class="form-label">Fecha de Nacimiento</label>
        <input type="date" class="form-control" id="birth_date" name="birth_date" value="<?php echo oldValue('birth_date', $employee ?? []); ?>" required>
    </div>
    <div class="col-md-4 mb-3">
        <label for="marital_status" class="form-label">Estado Civil</label>
        <select class="form-select" id="marital_status" name="marital_status">
            <option value="Soltero" <?php echo setSelected('marital_status', 'Soltero', $employee ?? []); ?>>Soltero(a)</option>
            <option value="Casado" <?php echo setSelected('marital_status', 'Casado', $employee ?? []); ?>>Casado(a)</option>
            <option value="Divorciado" <?php echo setSelected('marital_status', 'Divorciado', $employee ?? []); ?>>Divorciado(a)</option>
            <option value="Viudo" <?php echo setSelected('marital_status', 'Viudo', $employee ?? []); ?>>Viudo(a)</option>
            <option value="Unión Libre" <?php echo setSelected('marital_status', 'Unión Libre', $employee ?? []); ?>>Unión Libre</option>
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label for="dependents" class="form-label">Personas a Cargo</label>
        <input type="number" class="form-control" id="dependents" name="dependents" value="<?php echo oldValue('dependents', $employee ?? []); ?>" min="0" default="0">
    </div>
</div>

<hr>

<!-- Información Académica y Laboral -->
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="education_level" class="form-label">Nivel Educativo</label>
        <input type="text" class="form-control" id="education_level" name="education_level" value="<?php echo oldValue('education_level', $employee ?? []); ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label for="current_position" class="form-label">Cargo Actual</label>
        <input type="text" class="form-control" id="current_position" name="current_position" value="<?php echo oldValue('current_position', $employee ?? []); ?>">
    </div>
    <div class="col-12 mb-3">
        <label for="work_experience" class="form-label">Experiencia Laboral</label>
        <textarea class="form-control" id="work_experience" name="work_experience" rows="3"><?php echo oldValue('work_experience', $employee ?? []); ?></textarea>
    </div>
    <div class="col-12 mb-3">
        <label for="licenses_certifications" class="form-label">Licencias y Certificaciones</label>
        <textarea class="form-control" id="licenses_certifications" name="licenses_certifications" rows="3"><?php echo oldValue('licenses_certifications', $employee ?? []); ?></textarea>
    </div>
</div>

<hr>

<!-- Información del Contrato -->
<div class="row">
    <div class="col-md-4 mb-3">
        <label for="department" class="form-label">Departamento</label>
        <input type="text" class="form-control" id="department" name="department" value="<?php echo oldValue('department', $employee ?? []); ?>">
    </div>
    <div class="col-md-4 mb-3">
        <label for="immediate_boss" class="form-label">Jefe Inmediato</label>
        <input type="text" class="form-control" id="immediate_boss" name="immediate_boss" value="<?php echo oldValue('immediate_boss', $employee ?? []); ?>">
    </div>
     <div class="col-md-4 mb-3">
        <label for="hire_date" class="form-label">Fecha de Contratación</label>
        <input type="date" class="form-control" id="hire_date" name="hire_date" value="<?php echo oldValue('hire_date', $employee ?? []); ?>">
    </div>
    <div class="col-md-4 mb-3">
        <label for="contract_type" class="form-label">Tipo de Contrato</label>
        <select class="form-select" id="contract_type" name="contract_type">
            <option value="Indefinido" <?php echo setSelected('contract_type', 'Indefinido', $employee ?? []); ?>>Indefinido</option>
            <option value="Término Fijo" <?php echo setSelected('contract_type', 'Término Fijo', $employee ?? []); ?>>Término Fijo</option>
            <option value="Prestación Servicios" <?php echo setSelected('contract_type', 'Prestación Servicios', $employee ?? []); ?>>Prestación de Servicios</option>
            <option value="Obra/Labor" <?php echo setSelected('contract_type', 'Obra/Labor', $employee ?? []); ?>>Obra o Labor</option>
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label for="work_schedule" class="form-label">Horario Laboral</label>
        <input type="text" class="form-control" id="work_schedule" name="work_schedule" value="<?php echo oldValue('work_schedule', $employee ?? []); ?>">
    </div>
    <div class="col-md-4 mb-3">
        <label for="pending_vacations" class="form-label">Días de Vacaciones Pendientes</o=>
        <input type="number" class="form-control" id="pending_vacations" name="pending_vacations" value="<?php echo oldValue('pending_vacations', $employee ?? []); ?>" min="0" default="0">
    </div>
</div>

<hr>

<!-- Información de Seguridad Social y Pago -->
<div class="row">
    <div class="col-md-4 mb-3">
        <label for="eps" class="form-label">EPS</label>
        <input type="text" class="form-control" id="eps" name="eps" value="<?php echo oldValue('eps', $employee ?? []); ?>">
    </div>
    <div class="col-md-4 mb-3">
        <label for="afp" class="form-label">Fondo de Pensiones (AFP)</label>
        <input type="text" class="form-control" id="afp" name="afp" value="<?php echo oldValue('afp', $employee ?? []); ?>">
    </div>
    <div class="col-md-4 mb-3">
        <label for="arl" class="form-label">ARL</label>
        <input type="text" class="form-control" id="arl" name="arl" value="<?php echo oldValue('arl', $employee ?? []); ?>">
    </div>
    <div class="col-md-4 mb-3">
        <label for="salary" class="form-label">Salario</label>
        <input type="number" class="form-control" id="salary" name="salary" step="0.01" value="<?php echo oldValue('salary', $employee ?? []); ?>">
    </div>
    <div class="col-md-4 mb-3">
        <label for="allowances" class="form-label">Auxilios/Bonificaciones</label>
        <input type="number" class="form-control" id="allowances" name="allowances" step="0.01" value="<?php echo oldValue('allowances', $employee ?? []); ?>">
    </div>
    <div class="col-md-4 mb-3">
        <label for="payment_frequency" class="form-label">Frecuencia de Pago</label>
        <select class="form-select" id="payment_frequency" name="payment_frequency">
            <option value="Mensual" <?php echo setSelected('payment_frequency', 'Mensual', $employee ?? []); ?>>Mensual</option>
            <option value="Quincenal" <?php echo setSelected('payment_frequency', 'Quincenal', $employee ?? []); ?>>Quincenal</option>
            <option value="Semanal" <?php echo setSelected('payment_frequency', 'Semanal', $employee ?? []); ?>>Semanal</option>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label for="bank_name" class="form-label">Nombre del Banco</label>
        <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?php echo oldValue('bank_name', $employee ?? []); ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label for="bank_account" class="form-label">Número de Cuenta Bancaria</label>
        <input type="text" class="form-control" id="bank_account" name="bank_account" value="<?php echo oldValue('bank_account', $employee ?? []); ?>">
    </div>
</div>

<hr>

<!-- Observaciones -->
<div class="row">
    <div class="col-12 mb-3">
        <label for="performance_observations" class="form-label">Observaciones de Desempeño</label>
        <textarea class="form-control" id="performance_observations" name="performance_observations" rows="4"><?php echo oldValue('performance_observations', $employee ?? []); ?></textarea>
    </div>
</div>