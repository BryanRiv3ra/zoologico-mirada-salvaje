<?= $this->include('templates/header') ?>

<?php
  $cajero = trim(($venta['empleado_nombre'] ?? '') . ' ' . ($venta['empleado_apellido'] ?? ''));
?>

<div class="print-header">
  <div class="print-header__brand">Mirada Salvaje</div>
  <div class="print-header__row">
    <span><strong>Venta:</strong> <?= esc($venta['codigo']) ?></span>
    <span><strong>Fecha de venta:</strong> <?= esc(date('d/m/Y H:i', strtotime((string) $venta['fecha']))) ?></span>
    <span><strong>Impreso:</strong> <?= esc(date('d/m/Y H:i')) ?></span>
  </div>
  <div class="print-header__row">
    <span><strong>Visitante:</strong> <?= esc($venta['visitante_nombre'] ?? '—') ?></span>
  </div>
</div>

<div class="page-title-row flex-between no-print">
  <div>
    <h2>Venta <?= esc($venta['codigo']) ?></h2>
    <p><?= $cajero !== '' ? 'Cajero: ' . esc($cajero) . ' · ' : '' ?><?= esc($venta['fecha']) ?></p>
  </div>
  <div>
    <a href="<?= base_url('entradas/taquilla') ?>" class="btn-module">← Taquilla</a>
    <button type="button" data-imprimir class="btn-module">Imprimir</button>
  </div>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box">
  <div class="box-head flex-between">
    <h4>Boletos emitidos</h4>
    <span class="status-chip is-<?= esc($venta['estado']) ?>"><?= esc(ucfirst($venta['estado'])) ?></span>
  </div>

  <div class="table-container">
    <table class="styled-table">
      <thead>
        <tr><th>Tarifa</th><th>Fecha visita</th><th>Precio</th><th>Promoción</th><th>Código QR</th></tr>
      </thead>
      <tbody>
        <?php if ($venta['boletos'] === []): ?>
          <tr><td colspan="5" class="empty-state">Esta venta no tiene boletos.</td></tr>
        <?php endif; ?>
        <?php foreach ($venta['boletos'] as $boleto): ?>
          <tr>
            <td>
              <strong><?= esc($boleto['tarifa_nombre']) ?></strong>
              <div><small class="text-muted"><?= esc(ucwords(str_replace('_', ' ', $boleto['tipo_visitante']))) ?></small></div>
            </td>
            <td><?= esc($boleto['fecha_visita']) ?></td>
            <td>Q <?= number_format((float) $boleto['precio'], 2) ?></td>
            <td><?= $boleto['promocion_nombre'] ? esc($boleto['promocion_nombre']) : '—' ?></td>
            <td>
              <img src="<?= esc($qr->imagenUrl($boleto['codigo_qr'], 90)) ?>" alt="QR" class="qr-img">
              <div><code class="qr-code-text"><?= esc($boleto['codigo_qr']) ?></code></div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="form-table-layout" style="margin-top:16px;">
    <div class="content-box">
      <div class="box-head">
        <h4>Totales</h4>
      </div>
      <p>Subtotal: <strong>Q <?= number_format((float) $venta['subtotal'], 2) ?></strong></p>
      <p>Descuento: <strong>-Q <?= number_format((float) $venta['descuento'], 2) ?></strong></p>
      <p class="total-final">Total: <strong>Q <?= number_format((float) $venta['total'], 2) ?></strong></p>
      <p class="text-muted">Método: <?= esc(ucfirst($venta['tipo_pago'])) ?> · <?= esc(ucfirst($venta['tipo_venta'])) ?></p>
    </div>

    <div class="content-box no-print">
      <div class="box-head">
        <h4>Anulación</h4>
      </div>

      <?php if ($venta['estado'] === 'completada'): ?>
        <p class="text-muted" style="margin-top:0;">Se anulan todos los boletos y el pago queda sin efecto. Solo si ninguno fue usado en el ingreso.</p>
        <form method="post" action="<?= base_url('entradas/taquilla/anular/' . $venta['id']) ?>" class="modern-form">
          <?= csrf_field() ?>
          <div class="form-field">
            <label for="motivo">Motivo de anulación *</label>
            <textarea id="motivo" name="motivo" rows="3" maxlength="500" required
                      placeholder="Ej: Error de captura, el cliente desistió..."></textarea>
          </div>
          <button type="submit" class="submit-btn submit-btn--danger" style="max-width:220px;">Anular venta</button>
        </form>
      <?php else: ?>
        <p class="empty-state">
          Venta anulada el <?= esc($venta['fecha_anulacion'] ?? '—') ?>.<br>
          Motivo: <?= esc($venta['motivo_anulacion'] ?? '—') ?>
        </p>
      <?php endif; ?>
    </div>
  </div>
</div>

<?= $this->include('entradas/_imprimir_script') ?>

<?= $this->include('templates/footer') ?>