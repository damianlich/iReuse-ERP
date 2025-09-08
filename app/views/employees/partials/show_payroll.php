<!-- Contenido para la pestaña de Nómina -->
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <strong>Atención:</strong> Esta sección contiene información confidencial.
</div>

<h5 class="mt-4 mb-3 border-bottom pb-2">Información Salarial y de Pago</h5>
<div class="row">
    <div class="col-md-6">
        <p><span class="info-label">Salario Base:</span> <span class="info-value">$<?php echo number_format($employee['salary'], 2, ',', '.'); ?></span></p>
        <p><span class="info-label">Frecuencia de Pago:</span> <span class="info-value"><?php echo htmlspecialchars($employee['payment_frequency']); ?></span></p>
        <p><span class="info-label">Auxilios/Bonificaciones:</span> <span class="info-value">$<?php echo number_format($employee['allowances'], 2, ',', '.'); ?></span></p>
    </div>
    <div class="col-md-6">
        <p><span class="info-label">Banco:</span> <span class="info-value"><?php echo htmlspecialchars($employee['bank_name']); ?></span></p>
        <p><span class="info-label">Número de Cuenta:</span> <span class="info-value"><?php echo htmlspecialchars($employee['bank_account']); ?></span></p>
    </div>
</div>