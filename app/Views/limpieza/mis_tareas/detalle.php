<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between">
  <div>
    <h2>Detalle de Asignación</h2>
    <p><?= esc($asignacion['descripcion']) ?> · <?= esc($asignacion['zona_nombre']) ?></p>
  </div>
  <a href="<?= base_url('limpieza/mis-tareas') ?>" class="btn-module">← Mis tareas</a>
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

  <?php if ($asignacion['estado'] === 'pendiente'): ?>
    <p style="color:var(--text-muted);margin-bottom:16px;">Inicia la tarea para comenzar a trabajar en ella.</p>
    <form method="post" action="<?= base_url('limpieza/mis-tareas/iniciar/' . $asignacion['id']) ?>">
      <?= csrf_field() ?>
      <button type="submit" class="submit-btn" style="max-width:220px;">Iniciar tarea</button>
    </form>
  <?php endif; ?>

  <?php if ($asignacion['estado'] === 'en_curso'): ?>
    <form method="post" action="<?= base_url('limpieza/mis-tareas/finalizar/' . $asignacion['id']) ?>" class="modern-form">
      <?= csrf_field() ?>
      <div class="form-field">
        <label for="observaciones">Observaciones de la tarea (opcional, máximo 500 caracteres)</label>
        <textarea id="observaciones" name="observaciones" rows="4" maxlength="500"
                  placeholder="Ej: Se usaron 2 litros de desinfectante, quedó en buen estado."><?= esc(old('observaciones', $registro['observaciones'] ?? '')) ?></textarea>
      </div>
      <button type="submit" class="submit-btn" style="max-width:220px;">Finalizar tarea</button>
    </form>
  <?php endif; ?>

  <?php if ($asignacion['estado'] === 'listo'): ?>
    <p style="color:var(--text-muted);" class="empty-state">Tarea finalizada. Puedes agregar más observaciones si lo necesitas.</p>
  <?php endif; ?>

  <form method="post" action="<?= base_url('limpieza/mis-tareas/observaciones/' . $asignacion['id']) ?>" class="modern-form" style="margin-top:16px;">
    <?= csrf_field() ?>
    <div class="form-field">
      <label for="nueva_obs">Registrar observación</label>
      <textarea id="nueva_obs" name="observaciones" rows="3" maxlength="500"
                placeholder="Registra o actualiza las observaciones de la asignación."></textarea>
    </div>
    <button type="submit" class="submit-btn" style="max-width:220px;">Guardar observaciones</button>
  </form>
</div>

<?= $this->include('templates/footer') ?>