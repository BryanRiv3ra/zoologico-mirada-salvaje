<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between">
  <div>
    <h2>Promociones</h2>
    <p>Descuentos vigentes, aplicables a todas las tarifas activas.</p>
  </div>
  <a href="<?= base_url('entradas/promociones/nueva') ?>" class="btn-module">+ Nueva promoción</a>
</div>

<div class="content-box" style="display:flex; gap:8px; padding:12px 16px; margin-bottom:20px;">
  <a href="<?= base_url('entradas/taquilla') ?>" class="status-chip <?= strpos(current_url(), 'taquilla') !== false ? 'is-completada' : '' ?>" style="text-decoration:none; padding:8px 16px;">Taquilla</a>
  <a href="<?= base_url('entradas/tarifas') ?>" class="status-chip <?= strpos(current_url(), 'tarifas') !== false ? 'is-completada' : '' ?>" style="text-decoration:none; padding:8px 16px;">Tarifas</a>
  <a href="<?= base_url('entradas/promociones') ?>" class="status-chip <?= strpos(current_url(), 'promociones') !== false ? 'is-completada' : '' ?>" style="text-decoration:none; padding:8px 16px;">Promociones</a>
  <a href="<?= base_url('entradas/reportes') ?>" class="status-chip <?= strpos(current_url(), 'reportes') !== false ? 'is-completada' : '' ?>" style="text-decoration:none; padding:8px 16px;">Reportes</a>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box">
  <div class="table-container">
    <table class="styled-table">
      <thead>
        <tr><th>Promoción</th><th>Código</th><th>Descuento</th><th>Vigencia</th><th>Estado</th><th></th></tr>
      </thead>
      <tbody>
        <?php if ($promos === []): ?>
          <tr><td colspan="6" class="empty-state">No hay promociones registradas.</td></tr>
        <?php endif; ?>
        <?php foreach ($promos as $promo): ?>
          <tr>
            <td><strong><?= esc($promo['nombre']) ?></strong></td>
            <td><?= esc($promo['codigo']) ?></td>
            <td><?= esc($promo['descuento']) ?>%</td>
            <td><?= esc($promo['fecha_inicio']) ?> — <?= esc($promo['fecha_fin']) ?></td>
            <td>
              <span class="status-chip is-<?= $promo['activo'] ? 'activa' : 'inactivo' ?>">
                <?= $promo['activo'] ? 'Activa' : 'Inactiva' ?>
              </span>
            </td>
            <td class="td-actions">
              <a href="<?= base_url('entradas/promociones/editar/' . $promo['id']) ?>">Editar</a>
              <?php if ($promo['activo']): ?>
                <form class="inline-form" method="post" action="<?= base_url('entradas/promociones/desactivar/' . $promo['id']) ?>"
                      onsubmit="return confirm('¿Desactivar esta promoción?');">
                  <?= csrf_field() ?>
                  <button type="submit" class="link-danger">Desactivar</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->include('templates/footer') ?>