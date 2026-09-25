<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between">
  <div>
    <h2><?= esc($titulo) ?></h2>
    <p>Los descuentos vigentes aplican a todas las tarifas activas.</p>
  </div>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box">
  <form method="post" action="<?= $promo === null ? base_url('entradas/promociones/guardar') : base_url('entradas/promociones/actualizar/' . $promo['id']) ?>">
    <?= csrf_field() ?>

    <div class="form-field">
      <label for="nombre">Nombre</label>
      <input type="text" id="nombre" name="nombre" value="<?= esc(old('nombre', $promo['nombre'] ?? '')) ?>" required>
    </div>

    <div class="form-field">
      <label for="descripcion">Descripción</label>
      <textarea id="descripcion" name="descripcion" rows="3"><?= esc(old('descripcion', $promo['descripcion'] ?? '')) ?></textarea>
    </div>

    <div class="form-field">
      <label for="codigo">Código</label>
      <input type="text" id="codigo" name="codigo" value="<?= esc(old('codigo', $promo['codigo'] ?? '')) ?>" required>
    </div>

    <div class="form-field">
      <label for="descuento">Descuento (%)</label>
      <input type="number" id="descuento" name="descuento" step="0.01" min="0.01" max="100"
             value="<?= esc(old('descuento', $promo['descuento'] ?? '')) ?>" required>
    </div>

    <div class="form-field">
      <label for="fecha_inicio">Vigente desde</label>
      <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= esc(old('fecha_inicio', $promo['fecha_inicio'] ?? '')) ?>" required>
    </div>

    <div class="form-field">
      <label for="fecha_fin">Vigente hasta</label>
      <input type="date" id="fecha_fin" name="fecha_fin" value="<?= esc(old('fecha_fin', $promo['fecha_fin'] ?? '')) ?>" required>
    </div>

    <button type="submit" class="submit-btn">Guardar</button>
    <a href="<?= base_url('entradas/promociones') ?>">Cancelar</a>
  </form>
</div>

<?= $this->include('templates/footer') ?>