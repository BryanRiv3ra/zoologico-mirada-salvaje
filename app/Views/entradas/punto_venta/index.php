<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between">
  <div>
    <h2>Taquilla</h2>
    <p>Ventas de entradas en punto de venta.</p>
  </div>
  <a href="<?= base_url('entradas/taquilla/nueva') ?>" class="btn-module">+ Nueva venta</a>
</div>

<div class="content-box" style="display:flex; gap:8px; padding:12px 16px; margin-bottom:20px;">
  <a href="<?= base_url('entradas/taquilla') ?>" class="status-chip <?= strpos(current_url(), 'taquilla') !== false ? 'is-completada' : '' ?>" style="text-decoration:none; padding:8px 16px;">Taquilla</a>
  <a href="<?= base_url('entradas/tarifas') ?>" class="status-chip <?= strpos(current_url(), 'tarifas') !== false ? 'is-completada' : '' ?>" style="text-decoration:none; padding:8px 16px;">Tarifas</a>
  <a href="<?= base_url('entradas/promociones') ?>" class="status-chip <?= strpos(current_url(), 'promociones') !== false ? 'is-completada' : '' ?>" style="text-decoration:none; padding:8px 16px;">Promociones</a>
  <a href="<?= base_url('entradas/reportes') ?>" class="status-chip <?= strpos(current_url(), 'reportes') !== false ? 'is-completada' : '' ?>" style="text-decoration:none; padding:8px 16px;">Reportes</a>
</div>

<?= $this->include('templates/alertas') ?>

<div class="stat-row">
  <div class="stat-pill">
    <span class="stat-pill__label">Ventas hoy</span>
    <strong class="stat-pill__val"><?= $resumenHoy['ventas'] ?></strong>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Boletos hoy</span>
    <strong class="stat-pill__val"><?= $resumenHoy['boletos'] ?></strong>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Monto hoy</span>
    <strong class="stat-pill__val">Q <?= number_format($resumenHoy['monto'], 2) ?></strong>
  </div>
</div>

<div class="content-box">
  <div class="box-head flex-between">
    <h4>Últimas ventas (30 días)</h4>
    <span class="counter-badge"><?= count($ventas) ?> ventas</span>
  </div>

  <div class="table-container">
    <table class="styled-table">
      <thead>
        <tr><th>Venta</th><th>Fecha</th><th>Empleado</th><th>Tipo</th><th>Pago</th><th>Total</th><th>Estado</th><th></th></tr>
      </thead>
      <tbody>
        <?php if ($ventas === []): ?>
          <tr><td colspan="8" class="empty-state">Sin ventas en el periodo.</td></tr>
        <?php endif; ?>
        <?php foreach ($ventas as $venta): ?>
          <tr>
            <td><strong><?= esc($venta['codigo']) ?></strong></td>
            <td><?= esc($venta['fecha']) ?></td>
            <td><?= esc(trim(($venta['empleado_nombre'] ?? '') . ' ' . ($venta['empleado_apellido'] ?? ''))) ?></td>
            <td><?= esc(ucfirst($venta['tipo_venta'])) ?></td>
            <td><?= esc(ucfirst($venta['tipo_pago'])) ?></td>
            <td><strong>Q <?= number_format((float) $venta['total'], 2) ?></strong></td>
            <td>
              <span class="status-chip is-<?= esc($venta['estado']) ?>"><?= esc(ucfirst($venta['estado'])) ?></span>
              <?php if ($venta['estado'] === 'anulada'): ?>
                <div><small class="text-muted"><?= esc($venta['motivo_anulacion']) ?></small></div>
              <?php endif; ?>
            </td>
            <td class="td-actions">
              <a href="<?= base_url('entradas/taquilla/detalle/' . $venta['id']) ?>">Ver</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->include('templates/footer') ?>