<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between">
  <div>
    <h2>Especies</h2>
    <p>Catálogo de especies del zoológico. Cada animal debe pertenecer a una.</p>
  </div>
  <a href="<?= base_url('animales') ?>" class="btn-module">← Volver a Animales</a>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box">
  <div class="box-head flex-between">
    <h4>Especies registradas</h4>
    <?php if ($puedeEditar): ?>
      <a href="<?= base_url('animales/especies/nueva') ?>" class="btn-module">+ Nueva especie</a>
    <?php endif; ?>
  </div>

  <div class="table-container">
    <table class="styled-table">
      <thead>
        <tr>
          <th>Nombre común</th>
          <th>Nombre científico</th>
          <th>Descripción</th>
          <th>Animales</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($especies)): ?>
          <tr>
            <td colspan="5" class="empty-state">No hay especies registradas todavía.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($especies as $especie): ?>
            <tr>
              <td><strong><?= esc($especie['nombre_comun']) ?></strong></td>
              <td><em><?= esc($especie['nombre_cientifico'] ?? '—') ?></em></td>
              <td><?= esc($especie['descripcion'] ?? '—') ?></td>
              <td>
                <?php if ($especie['animales'] > 0): ?>
                  <span class="status-chip is-activa"><?= (int) $especie['animales'] ?></span>
                <?php else: ?>
                  <span class="status-chip is-inactivo">0</span>
                <?php endif; ?>
              </td>
              <td class="td-actions">
                <?php if ($puedeEditar): ?>
                  <a href="<?= base_url('animales/especies/editar/' . $especie['id']) ?>">Editar</a>
                  <?php if ($especie['animales'] === 0): ?>
                    <form method="post" action="<?= base_url('animales/especies/eliminar/' . $especie['id']) ?>" class="inline-form" onsubmit="return confirm('¿Eliminar la especie <?= esc(addslashes($especie['nombre_comun'])) ?>?');">
                      <?= csrf_field() ?>
                      <button type="submit" class="link-danger">Eliminar</button>
                    </form>
                  <?php endif; ?>
                <?php else: ?>
                  <span style="color:var(--text-sub);font-size:0.8rem;">Solo lectura</span>
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
