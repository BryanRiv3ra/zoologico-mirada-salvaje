<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Entradas · Mirada Salvaje</title>
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
      <a href="<?= base_url('portal') ?>" class="is-active">Taquilla en línea</a>
      <a href="<?= base_url('portal/comprar') ?>">Comprar</a>
    </nav>
  </header>

  <main class="portal-main">

    <section class="portal-hero">
      <h1>Boleto de ingreso al Zoológico</h1>
      <p>Elige tu tarifa, aprovecha las promociones y recibe tu boleto con código QR al instante.</p>
    </section>

    <section class="content-box">
      <div class="box-head">
        <h4>Tarifas de ingreso</h4>
      </div>
      <div class="cards-grid">
        <?php foreach ($tarifas as $tarifa): ?>
          <div class="content-box compact">
            <h4><?= esc($tarifa['nombre']) ?></h4>
            <p class="price-tag">Q <?= number_format((float) $tarifa['precio'], 2) ?></p>
            <small class="text-muted"><?= esc(ucwords(str_replace('_', ' ', $tarifa['tipo_visitante']))) ?></small>
          </div>
        <?php endforeach; ?>
        <?php if ($tarifas === []): ?>
          <p class="empty-state" style="grid-column:1/-1;">No hay tarifas disponibles por el momento.</p>
        <?php endif; ?>
      </div>
    </section>

    <?php if ($promos !== []): ?>
      <section class="content-box">
        <div class="box-head">
          <h4>Promociones vigentes</h4>
          <span class="counter-badge"><?= count($promos) ?> activas</span>
        </div>
        <div class="cards-grid">
          <?php foreach ($promos as $item): ?>
            <div class="content-box compact promo-card">
              <span class="promo-pct">-<?= (float) $item['promocion']['descuento'] ?>%</span>
              <h4><?= esc($item['promocion']['nombre']) ?></h4>
              <p class="text-muted">Aplica a <?= (int) $item['tarifa_count'] ?> tarifa(s).</p>
              <small class="promo-codigo">Código: <strong><?= esc($item['promocion']['codigo']) ?></strong></small>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>

    <section style="text-align:center;margin:26px 0 10px;">
      <a class="btn-module btn-hero" href="<?= base_url('portal/comprar') ?>">Comprar ahora</a>
    </section>

  </main>

  <footer class="portal-footer">Mirada Salvaje · Análisis de Sistemas II</footer>
</body>
</html>