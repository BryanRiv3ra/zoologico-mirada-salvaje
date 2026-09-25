<?= $this->include('templates/header') ?>

<div class="page-title-row">
  <div>
    <h2>Seguimiento de Limpieza</h2>
    <p>Asignaciones del día agrupadas por estado.</p>
  </div>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box" style="margin-bottom:20px;">
  <form method="get" action="<?= base_url('limpieza/seguimiento') ?>" class="filter-row">
    <div class="form-field">
      <label for="fecha">Fecha</label>
      <input type="date" id="fecha" name="fecha" value="<?= esc($fecha) ?>">
    </div>
    <div class="form-field">
      <label for="zona">Zona</label>
      <select id="zona" name="zona">
        <option value="0">Todas</option>
        <?php foreach ($zonas as $zona): ?>
          <option value="<?= esc($zona['id']) ?>" <?= (int) $zonaFiltro === (int) $zona['id'] ? 'selected' : '' ?>><?= esc($zona['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-field">
      <label for="empleado">Empleado</label>
      <select id="empleado" name="empleado">
        <option value="0">Todos</option>
        <?php foreach ($empleados as $empleado): ?>
          <option value="<?= esc($empleado['id']) ?>" <?= (int) $empleadoFiltro === (int) $empleado['id'] ? 'selected' : '' ?>>
            <?= esc($empleado['nombre'] . ' ' . ($empleado['apellido'] ?? '')) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="submit-btn" style="align-self:flex-end;">Filtrar</button>
  </form>
</div>

<div class="stat-row">
  <div class="stat-pill">
    <span class="stat-pill__label">Pendientes</span>
    <strong class="stat-pill__val"><?= $conteos['pendiente'] ?></strong>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">En curso</span>
    <strong class="stat-pill__val"><?= $conteos['en_curso'] ?></strong>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Listas</span>
    <strong class="stat-pill__val"><?= $conteos['listo'] ?></strong>
  </div>
</div>

<div class="kanban-grid">
  <?php foreach (['pendiente' => 'Pendiente', 'en_curso' => 'En curso', 'listo' => 'Listas'] as $clave => $etiqueta): ?>
    <div class="kanban-col is-<?= $clave ?>">
      <div class="kanban-col__head">
        <span><?= $etiqueta ?></span>
        <span class="counter-badge"><?= $conteos[$clave] ?></span>
      </div>

      <?php if (empty($porEstado[$clave])): ?>
        <p class="empty-state">Sin asignaciones.</p>
      <?php endif; ?>

      <?php foreach ($porEstado[$clave] as $asignacion): ?>
        <div class="asignacion-card">
          <div class="asignacion-card__top">
            <strong><?= esc($asignacion['descripcion']) ?></strong>
            <span class="status-chip is-<?= esc($clave) ?>"><?= esc($etiqueta) ?></span>
          </div>
          <p><?= esc($asignacion['zona_nombre']) ?></p>
          <p>
            <small>Empleado: <?= esc($asignacion['empleado_nombre'] . ' ' . ($asignacion['empleado_apellido'] ?? '')) ?></small>
          </p>
          <?php if (! empty($asignacion['inicio_real'])): ?>
            <p><small>Inicio: <?= esc(date('H:i', strtotime($asignacion['inicio_real']))) ?></small></p>
          <?php endif; ?>
          <?php if (! empty($asignacion['fin_real'])): ?>
            <p><small>Fin: <?= esc(date('H:i', strtotime($asignacion['fin_real']))) ?></small></p>
          <?php endif; ?>
          <?php if (! empty($asignacion['observaciones'])): ?>
            <p class="obs-note"><?= esc($asignacion['observaciones']) ?></p>
          <?php endif; ?>
          <a class="btn-module btn-module--sm" href="<?= base_url('limpieza/seguimiento/detalle/' . $asignacion['id']) ?>">Ver detalle</a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
</div>

<?= $this->include('templates/footer') ?>