<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de ventas · Mirada Salvaje</title>
  <style>
    body { font-family: 'Segoe UI', Arial, sans-serif; color: #0f172a; margin: 30px; }
    h1 { font-size: 1.5rem; margin: 0 0 4px; }
    .meta { color: #64748b; font-size: 0.85rem; margin-bottom: 18px; }
    .resumen { display:flex; gap:28px; margin-bottom:18px; font-size:0.9rem; }
    .resumen b { font-size:1.2rem; display:block; }
    table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
    th, td { border: 1px solid #cbd5e1; padding: 7px 9px; text-align: left; }
    th { background: #f1f5f9; }
    .chip { padding: 2px 8px; border-radius: 10px; font-size: 0.72rem; font-weight: 700; }
    .completada { background:#f0fdf4; color:#15803d; }
    .anulada { background:#fef2f2; color:#991b1b; }
    @media print { body { margin: 10mm; } }
  </style>
</head>
<body>
  <h1>Reporte de ventas de entradas</h1>
  <p class="meta">
    Periodo: <?= esc($filtros['desde']) ?> al <?= esc($filtros['hasta']) ?>
    · Método: <?= $filtros['tipo_pago'] === '' ? 'todos' : esc(ucfirst($filtros['tipo_pago'])) ?>
    · Generado: <?= esc($generado) ?>
  </p>

  <div class="resumen">
    <div>Ventas <b><?= $totales['ventas'] ?></b></div>
    <div>Completadas <b><?= $totales['completadas'] ?></b></div>
    <div>Anuladas <b><?= $totales['anuladas'] ?></b></div>
    <div>Boletos <b><?= $totales['boletos'] ?></b></div>
    <div>Ingresos <b>Q <?= number_format($totales['ingresos'], 2) ?></b></div>
    <div>Descuentos <b>Q <?= number_format($totales['descuentos'], 2) ?></b></div>
  </div>

  <table>
    <thead>
      <tr><th>Venta</th><th>Fecha</th><th>Empleado</th><th>Tipo</th><th>Pago</th><th>Subtotal</th><th>Desc.</th><th>Total</th><th>Estado</th></tr>
    </thead>
    <tbody>
      <?php if ($ventas === []): ?>
        <tr><td colspan="9">Sin ventas en el rango.</td></tr>
      <?php else: ?>
        <?php foreach ($ventas as $venta): ?>
          <tr>
            <td><?= esc($venta['codigo']) ?></td>
            <td><?= esc($venta['fecha']) ?></td>
            <td><?= esc(trim(($venta['empleado_nombre'] ?? '') . ' ' . ($venta['empleado_apellido'] ?? ''))) ?></td>
            <td><?= esc($venta['tipo_venta']) ?></td>
            <td><?= esc($venta['tipo_pago']) ?></td>
            <td><?= number_format((float) $venta['subtotal'], 2) ?></td>
            <td><?= number_format((float) $venta['descuento'], 2) ?></td>
            <td><?= number_format((float) $venta['total'], 2) ?></td>
            <td><span class="chip <?= esc($venta['estado']) ?>"><?= esc($venta['estado']) ?></span></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <script>window.print();</script>
</body>
</html>