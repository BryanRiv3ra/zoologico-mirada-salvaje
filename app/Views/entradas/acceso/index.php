<?= $this->include('templates/header') ?>

<div class="page-title-row">
  <div>
    <h2>Control de acceso</h2>
    <p>Valida el código QR de cada boleto en el ingreso del parque.</p>
  </div>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box">
  <div class="box-head">
    <h4>Buscar boleto</h4>
  </div>

  <form method="get" action="<?= base_url('entradas/acceso/buscar') ?>" class="filter-row">
    <div class="form-field" style="min-width:340px;">
      <label for="q">Código del boleto</label>
      <input type="text" id="q" name="q" value="<?= esc($codigo) ?>" required placeholder="MS-AAAAAAAAAAAAAA"
             style="font-family:var(--font-mono,monospace);">
    </div>
    <button type="submit" class="submit-btn" style="align-self:flex-end;">Validar</button>
  </form>

  <?php if ($codigo !== '' && $boleto === null): ?>
    <p class="empty-state" style="margin-top:18px;">No se encontró un boleto con el código <strong><?= esc($codigo) ?></strong>.</p>
  <?php endif; ?>

  <?php if ($boleto !== null): ?>
    <div class="form-table-layout" style="margin-top:18px;">
      <div class="content-box">
        <div class="box-head flex-between">
          <h4>Boleto <?= esc($boleto['codigo_qr']) ?></h4>
          <span class="status-chip is-<?= esc($boleto['estado']) ?>"><?= esc(ucfirst($boleto['estado'])) ?></span>
        </div>

        <table class="styled-table">
          <tbody>
            <tr><th>Tarifa</th><td><?= esc($boleto['tarifa_nombre']) ?> (<?= esc(ucwords(str_replace('_', ' ', $boleto['tipo_visitante']))) ?>)</td></tr>
            <tr><th>Fecha de visita</th><td><?= esc($boleto['fecha_visita']) ?></td></tr>
            <tr><th>Venta</th><td><?= esc($boleto['venta_codigo']) ?> · <?= esc($boleto['venta_fecha']) ?></td></tr>
            <tr><th>Origen</th><td><?= esc(ucfirst($boleto['tipo_venta'])) ?></td></tr>
            <tr><th>Precio</th><td>Q <?= number_format((float) $boleto['precio'], 2) ?></td></tr>
          </tbody>
        </table>

        <?php if ($boleto['estado'] === 'emitido'): ?>
          <form method="post" action="<?= base_url('entradas/acceso/validar/' . $boleto['id']) ?>">
            <?= csrf_field() ?>
            <button type="submit" class="submit-btn" style="max-width:240px;">Marcar como usado (ingreso)</button>
          </form>
        <?php else: ?>
          <p class="empty-state" style="color:var(--color-coral);">Acceso rechazado: el boleto ya no está vigente.</p>
        <?php endif; ?>
      </div>

      <div style="text-align:center;">
        <img src="<?= esc((new \App\Libraries\Entradas\GeneradorQr())->imagenUrl($boleto['codigo_qr'], 200)) ?>" alt="QR" class="qr-img qr-img--lg" loading="lazy">
      </div>
    </div>
  <?php endif; ?>
</div>

<?= $this->include('templates/footer') ?>