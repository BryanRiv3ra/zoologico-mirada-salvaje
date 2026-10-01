<?= $this->include('templates/header') ?>

<div class="page-title-row">
  <div>
    <h2><?= $modo === 'crear' ? 'Nueva Tarea de Limpieza' : 'Editar Tarea de Limpieza' ?></h2>
    <p>Solo se pueden crear tareas en zonas activas.</p>
  </div>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box" style="max-width:640px;">
  <form method="post"
        action="<?= $modo === 'crear' ? base_url('limpieza/tareas/guardar') : base_url('limpieza/tareas/actualizar/' . $tarea['id']) ?>"
        class="modern-form">
    <?= csrf_field() ?>

    <div class="form-field">
      <label for="zona_id">Zona *</label>
      <select id="zona_id" name="zona_id" required>
        <option value="">Selecciona una zona activa</option>
        <?php foreach ($zonas as $zona): ?>
          <option value="<?= esc($zona['id']) ?>" <?= (int) old('zona_id', $tarea['zona_id']) === (int) $zona['id'] ? 'selected' : '' ?>>
            <?= esc($zona['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-field">
      <label for="descripcion">Descripción de la tarea *</label>
      <textarea id="descripcion" name="descripcion" rows="3" maxlength="255" required><?= esc(old('descripcion', $tarea['descripcion'])) ?></textarea>
    </div>

    <div class="form-field">
      <label for="frecuencia">Frecuencia</label>
      <input type="text" id="frecuencia" name="frecuencia" maxlength="100"
             value="<?= esc(old('frecuencia', $tarea['frecuencia'] ?? '')) ?>" placeholder="Ej: Diaria, Semanal">
    </div>

    <button type="submit" class="submit-btn"><?= $modo === 'crear' ? 'Crear Tarea' : 'Guardar Cambios' ?></button>
  </form>
</div>

<?= $this->include('templates/footer') ?>