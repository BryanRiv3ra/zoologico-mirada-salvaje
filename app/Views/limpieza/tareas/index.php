<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between">
  <div>
    <h2>Tareas de Limpieza</h2>
    <p>Actividades programables por zona y sus insumos asociados.</p>
  </div>
  <a href="<?= base_url('limpieza/tareas/nueva') ?>" class="btn-module">+ Nueva tarea</a>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box">
  <div class="box-head flex-between">
    <h4>Tareas registradas</h4>
    <span class="counter-badge"><?= count($tareas) ?> total</span>
  </div>

  <div class="table-container">
    <table class="styled-table">
      <thead>
        <tr>
          <th>Tarea</th>
          <th>Zona</th>
          <th>Frecuencia</th>
          <th>Estado</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($tareas)): ?>
          <tr>
            <td colspan="5" class="empty-state">No hay tareas registradas.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($tareas as $tarea): ?>
            <tr>
              <td><strong><?= esc($tarea['descripcion']) ?></strong></td>
              <td><?= esc($tarea['zona_nombre']) ?></td>
              <td><?= esc($tarea['frecuencia'] ?? '—') ?></td>
              <td>
                <span class="status-chip is-<?= (int) $tarea['activo'] === 1 ? 'activa' : 'inactivo' ?>">
                  <?= (int) $tarea['activo'] === 1 ? 'Activa' : 'Inactiva' ?>
                </span>
              </td>
              <td class="td-actions">
                <a href="<?= base_url('limpieza/tareas/editar/' . $tarea['id']) ?>">Editar</a>
                <a href="<?= base_url('limpieza/tareas/insumos/' . $tarea['id']) ?>">Insumos</a>
                <?php if ((int) $tarea['activo'] === 1): ?>
                  <form method="post" action="<?= base_url('limpieza/tareas/desactivar/' . $tarea['id']) ?>" class="inline-form" onsubmit="return confirm('¿Desactivar esta tarea?');">
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