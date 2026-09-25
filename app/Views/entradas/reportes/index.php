<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between">
  <div>
    <h2>Reporte de ventas</h2>
    <p>Ingresos por venta de entradas en un rango de fechas.</p>
  </div>
  <div>
    <a href="<?= base_url('entradas/reportes/exportar') ?>?<?= http_build_query($filtros) ?>" class="btn-module">Exportar CSV</a>
    <a href="<?= base_url('entradas/reportes/imprimir') ?>?<?= http_build_query($filtros) ?>" class="btn-module">Imprimir</a>
  </div>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box" style="margin-bottom:20px;">
  <form method="get" action="<?= base_url('entradas/reportes') ?>" class="filter-row">
    <div class="form-field">
      <label for="desde">Desde</label>
      <input type="date" id="desde" name="desde" value="<?= esc($filtros['desde']) ?>">
    </div>
    <div class="form-field">
      <label for="hasta">Hasta</label>
      <input type="date" id="hasta" name="hasta" value="<?= esc($filtros['hasta']) ?>">
    </div>
    <div class="form-field">
      <label for="tipo_pago">Método de pago</label>
      <select id="tipo_pago" name="tipo_pago">
        <?php foreach ($metodos as $metodo): ?>
          <option value="<?= esc($metodo) ?>" <?= $filtros['tipo_pago'] === $metodo ? 'selected' : '' ?>>
            <?= $metodo === '' ? 'Todos' : ucfirst($metodo) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-field">
      <label for="empleado">Cajero</label>
      <select id="empleado" name="empleado">
        <option value="0">Todos</option>
        <?php foreach ($empleados as $empleado): ?>
          <option value="<?= esc($empleado['id']) ?>" <?= (int) $filtros['empleado'] === (int) $empleado['id'] ? 'selected' : '' ?>>
            <?= esc(trim(($empleado['nombre'] ?? '') . ' ' . ($empleado['apellido'] ?? ''))) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="submit-btn" style="align-self:flex-end;">Generar</button>
  </form>
</div>

<div class="stat-row">
  <div class="stat-pill">
    <span class="stat-pill__label">Ventas (periodo)</span>
    <strong class="stat-pill__val"><?= $totales['ventas'] ?></strong>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Completadas</span>
    <strong class="stat-pill__val"><?= $totales['completadas'] ?></strong>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Anuladas</span>
    <strong class="stat-pill__val" style="color:var(--color-coral);"><?= $totales['anuladas'] ?></strong>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Boletos emitidos</span>
    <strong class="stat-pill__val"><?= $totales['boletos'] ?></strong>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Ingresos</span>
    <strong class="stat-pill__val">Q <?= number_format($totales['ingresos'], 2) ?></strong>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Descuentos otorgados</span>
    <strong class="stat-pill__val">Q <?= number_format($totales['descuentos'], 2) ?></strong>
  </div>
  <?php foreach ($totales['porMetodo'] as $metodo => $monto): ?>
    <div class="stat-pill">
      <span class="stat-pill__label"><?= esc(ucfirst($metodo)) ?></span>
      <strong class="stat-pill__val">Q <?= number_format($monto, 2) ?></strong>
    </div>
  <?php endforeach; ?>
</div>

<div class="content-box">
  <div class="box-head flex-between">
    <h4>Detalle de ventas</h4>
    <span class="counter-badge"><?= $totales['ventas'] ?> registros</span>
  </div>

  <div class="table-container">
    <table class="styled-table">
      <thead>
        <tr><th>Venta</th><th>Fecha</th><th>Empleado</th><th>Tipo</th><th>Pago</th><th>Subtotal</th><th>Desc.</th><th>Total</th><th>Estado</th></tr>
      </thead>
      <tbody>
        <?php if ($ventas === []): ?>
          <tr><td colspan="9" class="empty-state">Sin ventas en el rango seleccionado.</td></tr>
        <?php endif; ?>
        <?php foreach ($ventas as $venta): ?>
          <tr>
            <td><strong><?= esc($venta['codigo']) ?></strong></td>
            <td><?= esc($venta['fecha']) ?></td>
            <td><?= esc(trim(($venta['empleado_nombre'] ?? '') . ' ' . ($venta['empleado_apellido'] ?? ''))) ?></td>
            <td><?= esc(ucfirst($venta['tipo_venta'])) ?></td>
            <td><?= esc(ucfirst($venta['tipo_pago'])) ?></td>
            <td>Q <?= number_format((float) $venta['subtotal'], 2) ?></td>
            <td>Q <?= number_format((float) $venta['descuento'], 2) ?></td>
            <td><strong>Q <?= number_format((float) $venta['total'], 2) ?></strong></td>
            <td>
              <span class="status-chip is-<?= esc($venta['estado']) ?>"><?= esc(ucfirst($venta['estado'])) ?></span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->include('templates/footer') ?>