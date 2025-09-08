<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="table-light">
            <tr><th>Fecha</th><th>Entrada</th><th>Salida</th><th>Total Horas</th></tr>
        </thead>
        <tbody>
            <?php if (!empty($attendance)): foreach ($attendance as $record): ?>
            <tr>
                <td><?php echo date("d/m/Y", strtotime($record['record_date'])); ?></td>
                <td><?php echo $record['entry_time'] ? date("h:i A", strtotime($record['entry_time'])) : '---'; ?></td>
                <td><?php echo $record['exit_time'] ? date("h:i A", strtotime($record['exit_time'])) : '---'; ?></td>
                <td><?php echo htmlspecialchars($record['total_hours'] ?? 'N/A'); ?></td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="4" class="text-center text-muted">No hay registros de asistencia para mostrar.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>