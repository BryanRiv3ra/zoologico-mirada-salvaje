<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between">
  <div>
    <h2><?= $tarifa === null ? 'Nueva tarifa' : 'Editar tarifa' ?></h2>
    <p>Configura el catálogo de precios de ingreso.</p>
  </div>
  <a href="<?= base_url('entradas/tarifas') ?>" class="btn-module">← Tarifas</a>
</div>

<?= $this->include('templates/alertas') ?>

<form method="post"
      action="<?= $tarifa === null ? base_url('entradas/tarifas/guardar') : base_url('entradas/tarifas/actualizar/' . $tarifa['id']) ?>"
      class="modern-form content-box" style="max-width:720px;">
  <?= csrf_field() ?>

  <div class="form-field">
    <label for="nombre">Nombre *</label>
    <input type="text" id="nombre" name="nombre" maxlength="100" required
           value="<?= esc(old('nombre', $tarifa['nombre'] ?? '')) ?>">
  </div>

  <div class="form-field">
    <label for="tipo_visitante">Tipo de visitante *</label>
    <select id="tipo_visitante" name="tipo_visitante">
      <?php foreach ($tipos as $valor => $etiqueta): ?>
        <option value="<?= esc($valor) ?>" <?= old('tipo_visitante', $tarifa['tipo_visitante'] ?? '') === $valor ? 'selected' : '' ?>>
          <?= esc($etiqueta) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="form-field" style="max-width:240px;">
    <label for="precio">Precio (Q) *</label>
    <input type="number" id="precio" name="precio" step="0.01" min="0.01" required
           value="<?= esc(old('precio', $tarifa['precio'] ?? '')) ?>">
  </div>

  <?php if ($tarifa !== null): ?>
    <div class="form-field" style="max-width:240px;">
      <label class="checkbox-line">
        <input type="checkbox" name="activo" value="1" <?= (int) $tarifa['activo'] === 1 ? 'checked' : '' ?>> Tarifa activa
      </label>
    </div>
  <?php endif; ?>

  <div style="display:flex;gap:12px;margin-top:6px;">
    <button type="submit" class="submit-btn" style="max-width:220px;"><?= $tarifa === null ? 'Crear tarifa' : 'Guardar cambios' ?></button>
    <a href="<?= base_url('entradas/tarifas') ?>" class="btn-module btn-module--ghost">Cancelar</a>
  </div>
</form>

<?= $this->include('templates/footer') ?>