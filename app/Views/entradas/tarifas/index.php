<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between">
  <div>
    <h2>Tarifas de ingreso</h2>
    <p>Catálogo de tarifas para el punto de venta y el portal.</p>
  </div>
  <a href="<?= base_url('entradas/tarifas/nueva') ?>" class="btn-module">+ Nueva tarifa</a>
</div>
<div class="content-box" style="display:flex; gap:8px; padding:12px 16px; margin-bottom:20px;">
  <a href="<?= base_url('entradas/taquilla') ?>" class="status-chip <?= strpos(current_url(), 'taquilla') !== false ? 'is-completada' : '' ?>" style="text-decoration:none; padding:8px 16px;">Taquilla</a>
  <a href="<?= base_url('entradas/tarifas') ?>" class="status-chip <?= strpos(current_url(), 'tarifas') !== false ? 'is-completada' : '' ?>" style="text-decoration:none; padding:8px 16px;">Tarifas</a>
  <a href="<?= base_url('entradas/reportes') ?>" class="status-chip <?= strpos(current_url(), 'reportes') !== false ? 'is-completada' : '' ?>" style="text-decoration:none; padding:8px 16px;">Reportes</a>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box">
  <div class="table-container">
    <table class="styled-table">
      <thead>
        <tr><th>Tarifa</th><th>Tipo de visitante</th><th>Precio</th><th>Estado</th><th></th></tr>
      </thead>
      <tbody>
        <?php if ($tarifas === []): ?>
          <tr><td colspan="5" class="empty-state">No hay tarifas registradas.</td></tr>
        <?php endif; ?>
        <?php foreach ($tarifas as $tarifa): ?>
          <tr>
            <td><strong><?= esc($tarifa['nombre']) ?></strong></td>
            <td><?= esc(ucwords(str_replace('_', ' ', $tarifa['tipo_visitante']))) ?></td>
            <td class="price-tag">Q <?= number_format((float) $tarifa['precio'], 2) ?></td>
            <td>
              <span class="status-chip is-<?= (int) $tarifa['activo'] === 1 ? 'activa' : 'inactivo' ?>">
                <?= (int) $tarifa['activo'] === 1 ? 'Activa' : 'Inactiva' ?>
              </span>
            </td>
            <td class="td-actions">
              <a href="<?= base_url('entradas/tarifas/editar/' . $tarifa['id']) ?>">Editar</a>
              <?php if ((int) $tarifa['activo'] === 1): ?>
                <form class="inline-form" method="post" action="<?= base_url('entradas/tarifas/desactivar/' . $tarifa['id']) ?>"
                      onsubmit="return confirm('¿Desactivar esta tarifa? Ya no se ofrecerá en ventas nuevas.');">
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