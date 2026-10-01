<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between">
  <div>
    <h2>Insumos de la Tarea</h2>
    <p>Materiales de limpieza asociados a: <strong><?= esc($tarea['descripcion']) ?></strong></p>
  </div>
  <a href="<?= base_url('limpieza/tareas') ?>" class="btn-module">← Volver a tareas</a>
</div>

<?= $this->include('templates/alertas') ?>

<div class="form-table-layout">
  <!-- Asociar insumo -->
  <div class="content-box">
    <div class="box-head">
      <h4>Asociar insumo</h4>
    </div>
    <form method="post" action="<?= base_url('limpieza/tareas/insumos/' . $tarea['id'] . '/agregar') ?>" class="modern-form">
      <?= csrf_field() ?>

      <div class="form-field">
        <label for="inventario_id">Insumo (tipo limpieza) *</label>
        <select id="inventario_id" name="inventario_id" required>
          <option value="">Selecciona un insumo</option>
          <?php foreach ($disponibles as $insumo): ?>
            <option value="<?= esc($insumo['id']) ?>">
              <?= esc($insumo['nombre']) ?> — Stock: <?= esc($insumo['stock_actual'] ?? 0) ?> <?= esc($insumo['unidad'] ?? '') ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if ($disponibles === []): ?>
          <small style="color:var(--text-muted);">No quedan insumos de limpieza sin asociar.</small>
        <?php endif; ?>
      </div>

      <div class="form-field">
        <label for="cantidad">Cantidad *</label>
        <input type="number" id="cantidad" name="cantidad" min="0.01" step="0.01" value="1" required>
      </div>

      <button type="submit" class="submit-btn">Asociar insumo</button>
    </form>
  </div>

  <!-- Listado de insumos asociados -->
  <div class="content-box">
    <div class="box-head flex-between">
      <h4>Insumos asociados</h4>
      <span class="counter-badge"><?= count($asignados) ?> asociados</span>
    </div>

    <div class="table-container">
      <table class="styled-table">
        <thead>
          <tr>
            <th>Insumo</th>
            <th>Cantidad</th>
            <th>Unidad</th>
            <th>Stock actual</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($asignados)): ?>
            <tr>
              <td colspan="5" class="empty-state">Esta tarea aún no tiene insumos asociados.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($asignados as $rel): ?>
              <tr>
                <td><strong><?= esc($rel['nombre']) ?></strong></td>
                <td><?= esc($rel['cantidad']) ?></td>
                <td><?= esc($rel['unidad'] ?? '—') ?></td>
                <td><?= esc($rel['stock_actual'] ?? '—') ?></td>
                <td class="td-actions">
                  <form method="post" action="<?= base_url('limpieza/tareas/insumos/quitar/' . $rel['id']) ?>" class="inline-form" onsubmit="return confirm('¿Retirar este insumo de la tarea?');">
                    <?= csrf_field() ?>
                    <button type="submit" class="link-danger">Quitar</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?= $this->include('templates/footer') ?>