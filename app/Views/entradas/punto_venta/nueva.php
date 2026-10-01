<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between">
  <div>
    <h2>Nueva venta de entradas</h2>
    <p>Taquilla · <?= date('d M, Y') ?></p>
  </div>
  <a href="<?= base_url('entradas/taquilla') ?>" class="btn-module">← Taquilla</a>
</div>

<?= $this->include('templates/alertas') ?>

<form method="post" action="<?= base_url('entradas/taquilla/guardar') ?>" class="modern-form">
  <?= csrf_field() ?>

  <div class="content-box">
    <div class="box-head">
      <h4>Entradas</h4>
    </div>

    <div class="filter-row" style="margin-bottom:14px;">
      <div class="form-field">
        <label for="fecha_visita">Fecha de visita</label>
        <input type="date" id="fecha_visita" name="fecha_visita" value="<?= esc(old('fecha_visita', $fechaVisita)) ?>" required min="<?= date('Y-m-d') ?>">
      </div>
      <div class="form-field">
        <label for="punto_venta">Punto de venta</label>
        <input type="text" id="punto_venta" name="punto_venta" value="<?= esc(old('punto_venta', 'Taquilla principal')) ?>" maxlength="50">
      </div>
      <div class="form-field">
        <label for="metodo_pago">Método de pago</label>
        <select id="metodo_pago" name="metodo_pago">
          <?php foreach ($metodosPago as $metodo): ?>
            <option value="<?= esc($metodo) ?>" <?= old('metodo_pago', 'efectivo') === $metodo ? 'selected' : '' ?>><?= esc(ucfirst($metodo)) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="table-container">
      <table class="styled-table">
        <thead>
          <tr><th>Tarifa</th><th>Precio</th><th>Cantidad</th><th>Promoción</th></tr>
        </thead>
        <tbody>
          <?php foreach ($tarifas as $tarifa): ?>
            <tr>
              <td>
                <strong><?= esc($tarifa['nombre']) ?></strong>
                <div><small class="text-muted"><?= esc(ucwords(str_replace('_', ' ', $tarifa['tipo_visitante']))) ?></small></div>
              </td>
              <td class="price-tag">Q <?= number_format((float) $tarifa['precio'], 2) ?></td>
              <td>
                <input type="number" class="input-qty" name="cantidad[<?= (int) $tarifa['id'] ?>]"
                       min="0" max="99" value="<?= esc((int) old('cantidad.' . $tarifa['id'] ?? 0)) ?>">
              </td>
              <td>
                <label class="radio-line">
                  <input type="radio" name="promo[<?= (int) $tarifa['id'] ?>]" value="0" checked> Sin promoción
                </label>
                <?php foreach (($promosPorTarifa[(int) $tarifa['id']] ?? []) as $promo): ?>
                  <label class="radio-line">
                    <input type="radio" name="promo[<?= (int) $tarifa['id'] ?>]" value="<?= (int) $promo['id'] ?>">
                    -<?= (float) $promo['descuento'] ?>% · <?= esc($promo['nombre']) ?>
                  </label>
                <?php endforeach; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if ($tarifas === []): ?>
            <tr><td colspan="4" class="empty-state">No hay tarifas activas. Configúralas desde <a href="<?= base_url('entradas/tarifas') ?>">Tarifas</a>.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="content-box" style="margin-top:18px;">
    <div class="box-head">
      <h4>Cliente (opcional)</h4>
    </div>
    <p class="text-muted" style="margin-top:0;">Si identificas al cliente se guardará su compra. En ventas anónimas se omite.</p>
    <div class="filter-row">
      <div class="form-field">
        <label for="cliente_nombre">Nombre</label>
        <input type="text" id="cliente_nombre" name="cliente_nombre" value="<?= esc(old('cliente_nombre')) ?>" maxlength="150">
      </div>
      <div class="form-field">
        <label for="cliente_email">Correo</label>
        <input type="email" id="cliente_email" name="cliente_email" value="<?= esc(old('cliente_email')) ?>" maxlength="150">
      </div>
      <div class="form-field">
        <label for="cliente_telefono">Teléfono</label>
        <input type="tel" id="cliente_telefono" name="cliente_telefono" value="<?= esc(old('cliente_telefono')) ?>" maxlength="20">
      </div>
    </div>
  </div>

  <div style="margin-top:20px;display:flex;gap:12px;">
    <button type="submit" class="submit-btn" style="max-width:220px;">Registrar venta</button>
    <a href="<?= base_url('entradas/taquilla') ?>" class="btn-module btn-module--ghost">Cancelar</a>
  </div>
</form>

<?= $this->include('templates/footer') ?>