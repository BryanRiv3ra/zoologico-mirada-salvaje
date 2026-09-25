<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between">
  <div>
    <h2><?= $promo === null ? 'Nueva promoción' : 'Editar promoción' ?></h2>
    <p>Define el descuento y las tarifas a las que aplica.</p>
  </div>
  <a href="<?= base_url('entradas/promociones') ?>" class="btn-module">← Promociones</a>
</div>

<?= $this->include('templates/alertas') ?>

<form method="post"
      action="<?= $promo === null ? base_url('entradas/promociones/guardar') : base_url('entradas/promociones/actualizar/' . $promo['id']) ?>"
      class="modern-form content-box" style="max-width:720px;">
  <?= csrf_field() ?>

  <div class="form-field">
    <label for="nombre">Nombre *</label>
    <input type="text" id="nombre" name="nombre" maxlength="150" required
           value="<?= esc(old('nombre', $promo['nombre'] ?? '')) ?>">
  </div>

  <div class="filter-row">
    <div class="form-field">
      <label for="codigo">Código *</label>
      <input type="text" id="codigo" name="codigo" maxlength="50" required
             value="<?= esc(old('codigo', $promo['codigo'] ?? '')) ?>"
             placeholder="Ej: PRIMAVERA25">
    </div>
    <div class="form-field" style="max-width:200px;">
      <label for="descuento">Descuento (%) *</label>
      <input type="number" id="descuento" name="descuento" step="0.5" min="0.01" max="100" required
             value="<?= esc(old('descuento', $promo['descuento'] ?? '')) ?>">
    </div>
  </div>

  <div class="filter-row">
    <div class="form-field">
      <label for="fecha_inicio">Fecha inicio *</label>
      <input type="date" id="fecha_inicio" name="fecha_inicio" required
             value="<?= esc(old('fecha_inicio', $promo['fecha_inicio'] ?? date('Y-m-d'))) ?>">
    </div>
    <div class="form-field">
      <label for="fecha_fin">Fecha fin *</label>
      <input type="date" id="fecha_fin" name="fecha_fin" required
             value="<?= esc(old('fecha_fin', $promo['fecha_fin'] ?? date('Y-m-d', strtotime('+30 days')))) ?>">
    </div>
  </div>

  <div class="form-field">
    <label for="descripcion">Descripción</label>
    <textarea id="descripcion" name="descripcion" rows="3"
              placeholder="Condiciones de la promoción..."><?= esc(old('descripcion', $promo['descripcion'] ?? '')) ?></textarea>
  </div>

  <div class="form-field">
    <label>Tarifas a las que aplica *</label>
    <div class="checkbox-grid">
      <?php foreach ($tarifas as $tarifa): ?>
        <label class="checkbox-line">
          <input type="checkbox" name="tarifas[]" value="<?= (int) $tarifa['id'] ?>"
                 <?= in_array((int) $tarifa['id'], $seleccionadas, true) ? 'checked' : '' ?>>
          <?= esc($tarifa['nombre']) ?> (Q <?= number_format((float) $tarifa['precio'], 2) ?>)
        </label>
      <?php endforeach; ?>
    </div>
    <small class="text-muted">Debes seleccionar al menos una tarifa para guardar.</small>
  </div>

  <div style="display:flex;gap:12px;margin-top:6px;">
    <button type="submit" class="submit-btn" style="max-width:220px;"><?= $promo === null ? 'Crear promoción' : 'Guardar cambios' ?></button>
    <a href="<?= base_url('entradas/promociones') ?>" class="btn-module btn-module--ghost">Cancelar</a>
  </div>
</form>

<?= $this->include('templates/footer') ?>