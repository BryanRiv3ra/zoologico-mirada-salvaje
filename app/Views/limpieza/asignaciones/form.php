<?= $this->include('templates/header') ?>

<div class="page-title-row">
  <div>
    <h2>Asignar Tarea de Limpieza</h2>
    <p>Programa una tarea a un empleado de limpieza.</p>
  </div>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box" style="max-width:640px;">
  <form method="post" action="<?= base_url('limpieza/asignaciones/guardar') ?>" class="modern-form">
    <?= csrf_field() ?>

    <div class="form-field">
      <label for="tarea_id">Tarea *</label>
      <select id="tarea_id" name="tarea_id" required>
        <option value="">Selecciona una tarea activa</option>
        <?php foreach ($tareas as $tarea): ?>
          <option value="<?= esc($tarea['id']) ?>" <?= (int) old('tarea_id') === (int) $tarea['id'] ? 'selected' : '' ?>>
            <?= esc($tarea['descripcion']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-field">
      <label for="empleado_id">Empleado de limpieza *</label>
      <select id="empleado_id" name="empleado_id" required>
        <option value="">Selecciona un empleado</option>
        <?php foreach ($empleados as $empleado): ?>
          <option value="<?= esc($empleado['id']) ?>" <?= (int) old('empleado_id') === (int) $empleado['id'] ? 'selected' : '' ?>>
            <?= esc($empleado['nombre'] . ' ' . ($empleado['apellido'] ?? '')) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-field">
      <label for="fecha_programada">Fecha programada *</label>
      <input type="date" id="fecha_programada" name="fecha_programada"
             value="<?= esc(old('fecha_programada', date('Y-m-d'))) ?>" min="<?= date('Y-m-d') ?>" required>
    </div>

    <button type="submit" class="submit-btn">Asignar tarea</button>
  </form>

  <p style="margin-top:16px;font-size:0.82rem;color:var(--text-muted);">
    No se puede asignar la misma tarea dos veces para la misma fecha.
  </p>
</div>

<?= $this->include('templates/footer') ?>