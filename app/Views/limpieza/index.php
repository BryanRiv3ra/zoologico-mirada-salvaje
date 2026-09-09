<?= $this->include('templates/header') ?>

  <div class="page-title-row">
    <div>
      <h2>Control de Limpieza</h2>
      <p>Gestión de cuadrillas y aseo en recintos e instalaciones.</p>
    </div>
  </div>

  <div class="form-table-layout">
    <!-- Formulario de Registro -->
    <div class="content-box">
      <div class="box-head">
        <h4>Registrar Nueva Área</h4>
      </div>
      <form method="post" action="<?= base_url('limpieza/guardar') ?>" class="modern-form">
        <div class="form-field">
          <label for="nombre">Nombre del Área</label>
          <input type="text" id="nombre" name="nombre" placeholder="Ej: Hábitat de Felinos Menores" required>
        </div>

        <div class="form-field">
          <label for="tipo">Tipo de Instalación</label>
          <select id="tipo" name="tipo">
            <option value="jaula">Recinto / Jaula</option>
            <option value="sanitario">Servicio Sanitario</option>
            <option value="jardin">Sendero / Jardín</option>
            <option value="juegos">Área Recreativa</option>
            <option value="oficina">Oficina Administrativa</option>
          </select>
        </div>

        <div class="form-field">
          <label for="responsable">Responsable Asignado</label>
          <input type="text" id="responsable" name="responsable" placeholder="Nombre completo" required>
        </div>

        <button type="submit" class="submit-btn">Guardar Asignación</button>
      </form>
    </div>

    <!-- Listado -->
    <div class="content-box">
      <div class="box-head flex-between">
        <h4>Áreas Registradas</h4>
        <span class="counter-badge"><?= isset($areas) ? count($areas) : 0 ?> total</span>
      </div>

      <div class="table-container">
        <table class="styled-table">
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
                <td colspan="4" class="empty-state">No hay áreas asignadas para hoy.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($areas as $area): ?>
                <tr>
                  <td><strong><?= esc($area['nombre']) ?></strong></td>
                  <td><span class="type-pill"><?= esc(ucfirst($area['tipo'])) ?></span></td>
                  <td><?= esc($area['responsable']) ?></td>
                  <td><span class="status-chip is-<?= esc($area['estado'] ?? 'pendiente') ?>"><?= esc(ucfirst($area['estado'] ?? 'Pendiente')) ?></span></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

<?= $this->include('templates/footer') ?>