<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Limpieza · Mirada Salvaje</title>
  <style>
    body { font-family: 'Segoe UI', Arial, sans-serif; color: #0f172a; margin: 30px; }
    h1 { font-size: 1.5rem; margin: 0 0 4px; }
    .meta { color: #64748b; font-size: 0.85rem; margin-bottom: 18px; }
    table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
    th, td { border: 1px solid #cbd5e1; padding: 7px 9px; text-align: left; }
    th { background: #f1f5f9; }
    .chip { padding: 2px 8px; border-radius: 10px; font-size: 0.72rem; font-weight: 700; }
    .pendiente { background:#fff7ed; color:#c2410c; }
    .en_curso { background:#eff6ff; color:#1d4ed8; }
    .listo { background:#f0fdf4; color:#15803d; }
    .resumen { display:flex; gap:24px; margin-bottom:18px; font-size:0.9rem; }
    .resumen b { font-size:1.2rem; display:block; }
    @media print { body { margin: 10mm; } }
  </style>
</head>
<body>
  <h1>Reporte de Limpieza</h1>
  <p class="meta">
    Periodo: <?= $filtros['desde'] !== '' ? esc($filtros['desde']) : 'inicio' ?> al <?= $filtros['hasta'] !== '' ? esc($filtros['hasta']) : 'fin' ?>
    · Generado: <?= esc($generado) ?>
  </p>

  <table>
    <thead>
      <tr>
        <th>Fecha</th>
        <th>Zona</th>
        <th>Tipo</th>
        <th>Tarea</th>
        <th>Empleado</th>
        <th>Estado</th>
        <th>Inicio</th>
        <th>Fin</th>
        <th>Observaciones</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($registros)): ?>
        <tr><td colspan="9">Sin asignaciones en el rango seleccionado.</td></tr>
      <?php else: ?>
        <?php foreach ($registros as $a): ?>
          <tr>
            <td><?= esc($a['fecha_programada']) ?></td>
            <td><?= esc($a['zona_nombre']) ?></td>
            <td><?= esc($a['zona_tipo']) ?></td>
            <td><?= esc($a['descripcion']) ?></td>
            <td><?= esc(trim(($a['empleado_nombre'] ?? '') . ' ' . ($a['empleado_apellido'] ?? ''))) ?></td>
            <td><span class="chip <?= esc($a['estado']) ?>"><?= esc(ucfirst(str_replace('_', ' ', $a['estado']))) ?></span></td>
            <td><?= esc($a['inicio_real'] ?? '—') ?></td>
            <td><?= esc($a['fin_real'] ?? '—') ?></td>
            <td><?= esc($a['observaciones'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <script>window.print();</script>
</body>
</html>