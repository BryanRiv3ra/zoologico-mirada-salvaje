<?= $this->include('templates/header') ?>

<div class="page-title-row">
  <div>
    <h2>Mis Asignaciones</h2>
    <p>Tareas de limpieza asignadas a ti.</p>
  </div>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box" style="margin-bottom:20px;">
  <form method="get" action="<?= base_url('limpieza/mis-tareas') ?>" class="filter-row">
    <div class="form-field">
      <label for="fecha">Filtrar por fecha</label>
      <input type="date" id="fecha" name="fecha" value="<?= esc($fecha) ?>">
    </div>
    <button type="submit" class="submit-btn" style="align-self:flex-end;">Filtrar</button>
  </form>
</div>

<div class="content-box">
  <div class="box-head flex-between">
    <h4>Mis tareas</h4>
    <span class="counter-badge"><?= count($asignaciones) ?> asignaciones</span>
  </div>

  <div class="cards-grid">
    <?php if (empty($asignaciones)): ?>
      <p class="empty-state" style="grid-column:1 / -1;">No tienes asignaciones<?= $fecha !== '' ? ' para esa fecha' : '' ?>.</p>
    <?php endif; ?>

    <?php foreach ($asignaciones as $asignacion): ?>
      <div class="content-box">
        <div class="box-head flex-between">
          <h4><?= esc($asignacion['descripcion']) ?></h4>
          <span class="status-chip is-<?= esc($asignacion['estado']) ?>"><?= esc(ucfirst(str_replace('_', ' ', $asignacion['estado']))) ?></span>
        </div>
        <p style="margin:0 0 6px;color:var(--text-muted);">
          <?= esc($asignacion['zona_nombre']) ?> · <small><?= esc(ucwords(str_replace('_', ' ', $asignacion['zona_tipo']))) ?></small>
        </p>
        <p style="margin:0 0 14px;color:var(--text-muted);">
          Fecha programada: <strong><?= esc($asignacion['fecha_programada']) ?></strong>
        </p>
        <a class="btn-module btn-module--sm" href="<?= base_url('limpieza/mis-tareas/detalle/' . $asignacion['id']) ?>">Ver detalle</a>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?= $this->include('templates/footer') ?>