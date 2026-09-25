<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between">
  <div>
    <h2>Zonas de Limpieza</h2>
    <p>Catálogo de áreas y recintos sujetos a mantenimiento.</p>
  </div>
  <a href="<?= base_url('limpieza/zonas/nueva') ?>" class="btn-module">+ Nueva zona</a>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box" style="margin-bottom:20px;">
  <form method="get" action="<?= base_url('limpieza/zonas') ?>" class="filter-bar">
    <div class="form-field" style="margin-bottom:0;">
      <label for="tipo">Filtrar por tipo de zona</label>
      <select name="tipo" id="tipo" class="auto-submit">
        <option value="">Todas</option>
        <?php foreach ($tipos as $valor => $etiqueta): ?>
          <option value="<?= esc($valor) ?>" <?= $tipoFiltro === $valor ? 'selected' : '' ?>><?= esc($etiqueta) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </form>
</div>

<div class="content-box">
  <div class="box-head flex-between">
    <h4>Zonas registradas</h4>
    <span class="counter-badge"><?= count($zonas) ?> total</span>
  </div>

  <div class="table-container">
    <table class="styled-table">
      <thead>
        <tr>
          <th>Zona</th>
          <th>Tipo</th>
          <th>Ubicación</th>
          <th>Capacidad</th>
          <th>Estado</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($zonas)): ?>
          <tr>
            <td colspan="6" class="empty-state">No hay zonas registradas con los filtros seleccionados.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($zonas as $zona): ?>
            <tr>
              <td><strong><?= esc($zona['nombre']) ?></strong></td>
              <td><span class="type-pill"><?= esc(ucwords(str_replace('_', ' ', $zona['tipo']))) ?></span></td>
              <td><?= esc($zona['ubicacion'] ?? '—') ?></td>
              <td><?= esc($zona['capacidad'] ?? '—') ?></td>
              <td>
                <span class="status-chip is-<?= (int) $zona['activo'] === 1 ? 'activa' : 'inactivo' ?>">
                  <?= (int) $zona['activo'] === 1 ? 'Activa' : 'Inactiva' ?>
                </span>
              </td>
              <td class="td-actions">
                <a href="<?= base_url('limpieza/zonas/editar/' . $zona['id']) ?>">Editar</a>
                <?php if ((int) $zona['activo'] === 1): ?>
                  <form method="post" action="<?= base_url('limpieza/zonas/desactivar/' . $zona['id']) ?>" class="inline-form" onsubmit="return confirm('¿Desactivar esta zona?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="link-danger">Desactivar</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->include('templates/footer') ?>