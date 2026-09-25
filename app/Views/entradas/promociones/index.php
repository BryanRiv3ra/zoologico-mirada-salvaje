<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between">
  <div>
    <h2>Promociones</h2>
    <p>Descuentos porcentuales aplicados a tarifas específicas.</p>
  </div>
  <a href="<?= base_url('entradas/promociones/nueva') ?>" class="btn-module">+ Nueva promoción</a>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box">
  <div class="table-container">
    <table class="styled-table">
      <thead>
        <tr><th>Promoción</th><th>Descuento</th><th>Vigencia</th><th>Tarifas</th><th>Estado</th><th></th></tr>
      </thead>
      <tbody>
        <?php if ($promos === []): ?>
          <tr><td colspan="6" class="empty-state">No hay promociones registradas.</td></tr>
        <?php endif; ?>
        <?php foreach ($promos as $item): ?>
          <?php $p = $item['promocion']; $hoy = date('Y-m-d'); $vigente = (int) $p['activo'] === 1 && $p['fecha_inicio'] <= $hoy && $p['fecha_fin'] >= $hoy; ?>
          <tr>
            <td>
              <strong><?= esc($p['nombre']) ?></strong>
              <div><small class="text-muted">Código: <?= esc($p['codigo']) ?></small></div>
            </td>
            <td class="promo-pct">-<?= (float) $p['descuento'] ?>%</td>
            <td><?= esc($p['fecha_inicio']) ?> → <?= esc($p['fecha_fin']) ?></td>
            <td>
              <?php if ($item['tarifas'] === []): ?>
                <span class="text-muted">(sin tarifas)</span>
              <?php else: ?>
                <?= esc(implode(', ', $item['tarifas'])) ?>
              <?php endif; ?>
            </td>
            <td>
              <span class="status-chip is-<?= $vigente ? 'activa' : 'inactivo' ?>">
                <?= $vigente ? 'Vigente' : 'Inactiva' ?>
              </span>
            </td>
            <td class="td-actions">
              <a href="<?= base_url('entradas/promociones/editar/' . $p['id']) ?>">Editar</a>
              <?php if ((int) $p['activo'] === 1): ?>
                <form class="inline-form" method="post" action="<?= base_url('entradas/promociones/desactivar/' . $p['id']) ?>"
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