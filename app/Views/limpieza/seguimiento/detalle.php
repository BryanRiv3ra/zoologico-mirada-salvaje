<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between">
  <div>
    <h2>Detalle de Asignación</h2>
    <p><?= esc($asignacion['descripcion']) ?> · <?= esc($asignacion['zona_nombre']) ?></p>
  </div>
  <a href="<?= base_url('limpieza/seguimiento') ?>" class="btn-module">← Seguimiento</a>
</div>

<?= $this->include('templates/alertas') ?>

<div class="form-table-layout">
  <div class="content-box">
    <div class="box-head flex-between">
      <h4>Asignación</h4>
      <span class="status-chip is-<?= esc($asignacion['estado']) ?>"><?= esc(ucfirst(str_replace('_', ' ', $asignacion['estado']))) ?></span>
    </div>

    <table class="styled-table">
      <tbody>
        <tr><th>Zona</th><td><?= esc($asignacion['zona_nombre']) ?> (<?= esc(ucwords(str_replace('_', ' ', $asignacion['zona_tipo']))) ?>)</td></tr>
        <tr><th>Tarea</th><td><?= esc($asignacion['descripcion']) ?></td></tr>
        <tr><th>Frecuencia</th><td><?= esc($asignacion['frecuencia'] ?? '—') ?></td></tr>
        <tr><th>Empleado</th><td><?= esc($asignacion['empleado_nombre'] . ' ' . ($asignacion['empleado_apellido'] ?? '')) ?></td></tr>
        <tr><th>Fecha programada</th><td><?= esc($asignacion['fecha_programada']) ?></td></tr>
        <tr><th>Inicio real</th><td><?= esc($asignacion['inicio_real'] ?? '—') ?></td></tr>
        <tr><th>Fin real</th><td><?= esc($asignacion['fin_real'] ?? '—') ?></td></tr>
      </tbody>
    </table>
  </div>

  <div class="content-box">
    <div class="box-head">
      <h4>Insumos necesarios</h4>
    </div>
    <?php if (empty($insumos)): ?>
      <p class="empty-state">Esta tarea no requiere insumos.</p>
    <?php else: ?>
      <div class="table-container">
        <table class="styled-table">
          <thead>
            <tr><th>Insumo</th><th>Cantidad</th><th>Stock</th></tr>
          </thead>
          <tbody>
            <?php foreach ($insumos as $insumo): ?>
              <tr>
                <td><?= esc($insumo['nombre']) ?></td>
                <td><?= esc($insumo['cantidad']) ?> <?= esc($insumo['unidad'] ?? '') ?></td>
                <td><?= esc($insumo['stock_actual'] ?? '—') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="content-box" style="margin-top:20px;">
  <div class="box-head">
    <h4>Observaciones</h4>
  </div>
  <?php if (empty($registro) || trim((string) ($registro['observaciones'] ?? '')) === ''): ?>
    <p class="empty-state">Sin observaciones registradas.</p>
  <?php else: ?>
    <p style="white-space:pre-line;"><?= esc($registro['observaciones']) ?></p>
    <p style="color:var(--text-muted);font-size:0.82rem;">
      Registrado el <?= esc(date('d/m/Y', strtotime($registro['fecha']))) ?> de <?= esc($registro['hora_inicio'] ?? '—') ?> a <?= esc($registro['hora_fin'] ?? '—') ?>
    </p>
  <?php endif; ?>
</div>

<?= $this->include('templates/footer') ?>