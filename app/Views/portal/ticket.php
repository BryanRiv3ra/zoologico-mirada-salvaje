<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Comprobante <?= esc($venta['codigo']) ?> · Mirada Salvaje</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/entradas.css') ?>">
</head>
<body class="portal-body">

  <main class="portal-main">
    <?= session('success') !== null ? '<div class="alert-box is-success">' . esc(session('success')) . '</div>' : '' ?>

    <div class="ticket-sheet">
      <div class="box-head flex-between">
        <h3>Comprobante de compra</h3>
        <span class="final-msg">PAGADO</span>
      </div>

      <div class="ticket-meta">
        <div><span>Venta:</span> <strong><?= esc($venta['codigo']) ?></strong></div>
        <div><span>Fecha:</span> <?= esc($venta['fecha']) ?></div>
        <div><span>Cliente:</span> <?= esc($venta['cliente_nombre'] ?? '—') ?></div>
        <div><span>Correo:</span> <?= esc($venta['cliente_email'] ?? '—') ?></div>
      </div>

      <table class="styled-table">
        <thead>
          <tr><th>Tarifa</th><th>Fecha visita</th><th>Precio</th><th>Promoción</th><th>Código QR</th></tr>
        </thead>
        <tbody>
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
                <img src="<?= esc($qr->imagenUrl($boleto['codigo_qr'], 90)) ?>"
                     alt="QR <?= esc($boleto['codigo_qr']) ?>" class="qr-img" loading="lazy">
                <div><code class="qr-code-text"><?= esc($boleto['codigo_qr']) ?></code></div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div class="ticket-totales">
        <div>Subtotal: <strong>Q <?= number_format((float) $venta['subtotal'], 2) ?></strong></div>
        <div>Descuento: <strong>-Q <?= number_format((float) $venta['descuento'], 2) ?></strong></div>
        <div class="total-final">Total pagado: <strong>Q <?= number_format((float) $venta['total'], 2) ?></strong></div>
      </div>

      <div class="ticket-footer">
        <div>
          <p class="text-muted">Presenta cada código QR en el ingreso.</p>
          <p class="text-muted">Método de pago: <?= esc(ucfirst($venta['tipo_pago'])) ?>.</p>
        </div>
        <div style="text-align:right;min-width:190px;">
          <button class="btn-module" onclick="window.print()">Imprimir</button>
          <a class="btn-module btn-module--ghost" href="<?= base_url('portal') ?>">Volver</a>
        </div>
      </div>
    </div>

  </main>

  <footer class="portal-footer">Mirada Salvaje · Análisis de Sistemas II</footer>
</body>
</html>