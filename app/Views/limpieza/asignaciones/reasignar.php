<?= $this->include('templates/header') ?>

<div class="page-title-row">
  <div>
    <h2>Reasignar Tarea</h2>
    <p>Cambia de empleado a una asignación todavía pendiente.</p>
  </div>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box" style="max-width:640px;">
  <div class="box-head">
    <h4><?= esc($asignacion['descripcion']) ?> · <?= esc($asignacion['zona_nombre']) ?></h4>
  </div>

  <p style="color:var(--text-muted);font-size:0.9rem;">
    Fecha programada: <strong><?= esc($asignacion['fecha_programada']) ?></strong> ·
    Empleado actual: <strong><?= esc($asignacion['empleado_nombre'] . ' ' . ($asignacion['empleado_apellido'] ?? '')) ?></strong>
  </p>

  <form method="post" action="<?= base_url('limpieza/asignaciones/reasignar/' . $asignacion['id']) ?>" class="modern-form">
    <?= csrf_field() ?>

    <div class="form-field">
      <label for="empleado_id">Nuevo empleado de limpieza *</label>
      <select id="empleado_id" name="empleado_id" required>
        <option value="">Selecciona un empleado</option>
        <?php foreach ($empleados as $empleado): ?>
          <option value="<?= esc($empleado['id']) ?>" <?= (int) $empleado['id'] === (int) $asignacion['empleado_id'] ? 'selected' : '' ?>>
            <?= esc($empleado['nombre'] . ' ' . ($empleado['apellido'] ?? '')) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <button type="submit" class="submit-btn">Reasignar</button>
  </form>
</div>

<?= $this->include('templates/footer') ?>