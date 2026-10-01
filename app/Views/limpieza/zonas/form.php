<?= $this->include('templates/header') ?>

<div class="page-title-row">
  <div>
    <h2><?= $modo === 'crear' ? 'Nueva Zona' : 'Editar Zona' ?></h2>
    <p><?= $modo === 'crear' ? 'Registra una nueva área del zoológico.' : 'Modifica los datos de la zona.' ?></p>
  </div>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box" style="max-width:640px;">
  <form method="post"
        action="<?= $modo === 'crear' ? base_url('limpieza/zonas/guardar') : base_url('limpieza/zonas/actualizar/' . $zona['id']) ?>"
        class="modern-form">
    <?= csrf_field() ?>

    <div class="form-field">
      <label for="nombre">Nombre de la zona *</label>
      <input type="text" id="nombre" name="nombre" maxlength="100"
             value="<?= esc(old('nombre', $zona['nombre'])) ?>" required>
    </div>

    <div class="form-field">
      <label for="tipo">Tipo de instalación *</label>
      <select id="tipo" name="tipo" required>
        <?php foreach ($tipos as $valor => $etiqueta): ?>
          <option value="<?= esc($valor) ?>" <?= old('tipo', $zona['tipo']) === $valor ? 'selected' : '' ?>>
            <?= esc($etiqueta) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-field">
      <label for="ubicacion">Ubicación</label>
      <input type="text" id="ubicacion" name="ubicacion" maxlength="150"
             value="<?= esc(old('ubicacion', $zona['ubicacion'] ?? '')) ?>" placeholder="Ej: Sector Norte, junto a felinos">
    </div>

    <div class="form-field">
      <label for="capacidad">Capacidad (personas)</label>
      <input type="number" id="capacidad" name="capacidad" min="0"
             value="<?= esc(old('capacidad', $zona['capacidad'] ?? '')) ?>">
    </div>

    <button type="submit" class="submit-btn"><?= $modo === 'crear' ? 'Crear Zona' : 'Guardar Cambios' ?></button>
  </form>
</div>

<?= $this->include('templates/footer') ?>