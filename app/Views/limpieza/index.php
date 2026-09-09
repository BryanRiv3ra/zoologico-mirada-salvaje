<?= $this->include('templates/header') ?>

  <div class="topbar">
    <div>
      <h1>Limpieza</h1>
      <p>Áreas registradas y su estado de limpieza.</p>
    </div>
  </div>

  <div class="panel">
    <div class="panel__header">
      <h2>Registrar área</h2>
    </div>

    <form method="post" action="<?= base_url('limpieza/guardar') ?>">
      <div class="form-group">
        <label for="nombre">Nombre del área</label>
        <input type="text" id="nombre" name="nombre" placeholder="Ej. Jaula de leones">
      </div>

      <div class="form-group">
        <label for="tipo">Tipo de área</label>
        <select id="tipo" name="tipo">
          <option value="jaula">Jaula</option>
          <option value="sanitario">Sanitario</option>
          <option value="jardin">Jardín</option>
          <option value="juegos">Área de juegos</option>
          <option value="oficina">Oficina</option>
        </select>
      </div>

      <div class="form-group">
        <label for="responsable">Responsable asignado</label>
        <input type="text" id="responsable" name="responsable" placeholder="Nombre del encargado">
      </div>

      <button type="submit" class="btn btn-primary">Guardar área</button>
    </form>
  </div>

  <div class="panel">
    <div class="panel__header">
      <h2>Áreas registradas</h2>
      <span style="font-size:0.85rem;color:var(--color-ink-soft)">
        <?= isset($areas) ? count($areas) : 0 ?> en total
      </span>
    </div>

    <table class="data-table">
      <thead>
        <tr>
          <th>Área</th>
          <th>Tipo</th>
          <th>Responsable</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($areas)): ?>
          <tr>
            <td colspan="4" style="color:var(--color-ink-soft)">Todavía no hay áreas registradas.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($areas as $area): ?>
            <tr>
              <td><?= esc($area['nombre']) ?></td>
              <td><?= esc($area['tipo']) ?></td>
              <td><?= esc($area['responsable']) ?></td>
              <td><?= esc($area['estado']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

<?= $this->include('templates/footer') ?>
