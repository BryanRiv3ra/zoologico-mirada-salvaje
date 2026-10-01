<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Comprar entradas · Mirada Salvaje</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/entradas.css') ?>">
</head>
<body class="portal-body">

  <header class="portal-top">
    <div class="brand-badge">
      <div class="brand-badge__icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a9 9 0 0 1 9 9c0 4.97-4.03 9-9 9s-9-4.03-9-9a9 9 0 0 1 9-9Z"/><path d="M12 6v6l4 2"/></svg>
      </div>
      <div class="brand-badge__text">
        <span>MIRADA SALVAJE</span>
        <small>Parque Zoológico</small>
      </div>
    </div>
    <nav class="portal-nav">
      <a href="<?= base_url('portal') ?>">Taquilla en línea</a>
      <a href="<?= base_url('portal/comprar') ?>" class="is-active">Comprar</a>
    </nav>
  </header>

  <main class="portal-main form-table-layout">

    <div class="content-box">
      <div class="box-head">
        <h4>Selecciona tus entradas</h4>
      </div>

      <?= session('success') !== null ? '<div class="alert-box is-success">' . esc(session('success')) . '</div>' : '' ?>
      <?= session('error') !== null ? '<div class="alert-box is-error">' . esc(session('error')) . '</div>' : '' ?>

      <form method="post" action="<?= base_url('portal/confirmar') ?>" class="modern-form">
        <?= csrf_field() ?>

        <div class="form-field">
          <label for="fecha_visita">Fecha de visita</label>
          <input type="date" id="fecha_visita" name="fecha_visita" value="<?= esc(old('fecha_visita', $fechaVisita)) ?>" required min="<?= date('Y-m-d') ?>">
        </div>

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
                         min="0" max="20" value="0">
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
              <tr><td colspan="4" class="empty-state">No hay tarifas disponibles.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>

        <div class="form-table-layout" style="margin-top:18px;">
          <div class="content-box">
            <div class="box-head">
              <h4>Datos del comprador</h4>
            </div>
            <div class="form-field">
              <label for="nombre">Nombre completo *</label>
              <input type="text" id="nombre" name="nombre" value="<?= esc(old('nombre')) ?>" required>
            </div>
            <div class="form-field">
              <label for="email">Correo electrónico *</label>
              <input type="email" id="email" name="email" value="<?= esc(old('email')) ?>" required>
            </div>
            <div class="form-field">
              <label for="telefono">Teléfono (opcional)</label>
              <input type="tel" id="telefono" name="telefono" value="<?= esc(old('telefono')) ?>">
            </div>
          </div>

          <div class="content-box">
            <div class="box-head">
              <h4>Pago</h4>
            </div>
            <p class="text-muted">
              El pago se procesa con una <strong>pasarela de pago simulada</strong>.
              Para esta demo el cargo se aprueba automáticamente:
            </p>
            <div class="pago-sim-box">
              <span>💳 Tarjeta de crédito / débito</span>
              <small>Nro. 4242 4242 4242 4242 · CAD 12/34 · CVC 777</small>
            </div>
            <button type="submit" class="submit-btn" style="margin-top:16px;max-width:100%;">Confirmar compra</button>
          </div>
        </div>
      </form>
    </div>

  </main>

  <footer class="portal-footer">Mirada Salvaje · Análisis de Sistemas II</footer>
</body>
</html>