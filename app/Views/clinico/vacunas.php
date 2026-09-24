<?= $this->include('templates/header') ?>

  <div class="page-title-row">
    <div>
      <h2>Calendario de Vacunas</h2>
      <p>Registro y control de vacunas aplicadas y programadas por animal.</p>
    </div>
    <a href="<?= base_url('clinico') ?>" class="submit-btn" style="text-decoration:none;">Ver tratamientos</a>
  </div>

  <?php if (session()->getFlashdata('mensaje')): ?>
    <div class="content-box" style="border-left:4px solid #10b981;margin-bottom:16px;">
      <?= esc(session()->getFlashdata('mensaje')) ?>
    </div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('errores')): ?>
    <div class="content-box" style="border-left:4px solid #ef4444;margin-bottom:16px;">
      <strong>No se pudo guardar:</strong>
      <ul style="margin:8px 0 0 18px;">
        <?php foreach (session()->getFlashdata('errores') as $error): ?>
          <li><?= esc($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="form-table-layout">
    <div class="content-box">
      <div class="box-head">
        <h4>Registrar Aplicación de Vacuna</h4>
      </div>

      <form method="post" action="<?= base_url('clinico/vacunas/guardar') ?>" class="modern-form">

        <div class="form-field">
          <label for="animal_id">Animal</label>
          <select id="animal_id" name="animal_id" required>
            <option value="">-- Selecciona un animal --</option>
            <?php foreach ($animales as $animal): ?>
              <option value="<?= esc($animal['id']) ?>" <?= old('animal_id') == $animal['id'] ? 'selected' : '' ?>>
                <?= esc($animal['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-field">
          <label for="vacuna_id">Vacuna</label>
          <select id="vacuna_id" name="vacuna_id" required>
            <option value="">-- Selecciona una vacuna --</option>
            <?php foreach ($vacunas as $vacuna): ?>
              <option value="<?= esc($vacuna['id']) ?>" <?= old('vacuna_id') == $vacuna['id'] ? 'selected' : '' ?>>
                <?= esc($vacuna['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (empty($vacunas)): ?>
            <small style="color:var(--text-muted, #64748b)">No hay vacunas en el catálogo todavía. Agrega una abajo.</small>
          <?php endif; ?>
        </div>

        <div class="form-field">
          <label for="veterinario_id">Veterinario</label>
          <select id="veterinario_id" name="veterinario_id" required>
            <option value="">-- Selecciona un veterinario --</option>
            <?php foreach ($empleados as $empleado): ?>
              <option value="<?= esc($empleado['id']) ?>" <?= old('veterinario_id') == $empleado['id'] ? 'selected' : '' ?>>
                <?= esc($empleado['nombre'] . ' ' . $empleado['apellido']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-field">
          <label for="fecha">Fecha de aplicación</label>
          <input type="date" id="fecha" name="fecha" value="<?= old('fecha') ?>" required>
          <small style="color:var(--text-muted, #64748b)">Una fecha futura la registra como "próxima"; una fecha pasada o de hoy, como "aplicada".</small>
        </div>

        <div class="form-field">
          <label for="dosis">Dosis (opcional)</label>
          <input type="text" id="dosis" name="dosis" placeholder="Ej: 1ml" value="<?= old('dosis') ?>">
        </div>

        <button type="submit" class="submit-btn">Guardar Vacuna</button>
      </form>
    </div>

    <div class="content-box">
      <div class="box-head">
        <h4>Agregar Vacuna al Catálogo</h4>
      </div>

      <form method="post" action="<?= base_url('clinico/vacunas/guardar-vacuna') ?>" class="modern-form">
        <div class="form-field">
          <label for="nombre">Nombre</label>
          <input type="text" id="nombre" name="nombre" placeholder="Ej: Rabia" required>
        </div>

        <div class="form-field">
          <label for="descripcion">Descripción (opcional)</label>
          <input type="text" id="descripcion" name="descripcion" placeholder="Ej: Refuerzo anual">
        </div>

        <button type="submit" class="submit-btn">Agregar al Catálogo</button>
      </form>
    </div>
  </div>

  <div class="content-box" style="margin-top:20px;">
    <div class="box-head flex-between">
      <h4>Próximas Vacunas</h4>
      <span class="counter-badge"><?= count($proximas) ?> programadas</span>
    </div>

    <div class="table-container">
      <table class="styled-table">
        <thead>
          <tr>
            <th>Animal</th>
            <th>Vacuna</th>
            <th>Veterinario</th>
            <th>Fecha</th>
            <th>Dosis</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($proximas)): ?>
            <tr>
              <td colspan="6" class="empty-state">No hay vacunas programadas a futuro.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($proximas as $v): ?>
              <tr>
                <td><strong><?= esc($v['animal']) ?></strong></td>
                <td><?= esc($v['vacuna']) ?></td>
                <td><?= esc($v['vet_nombre'] . ' ' . $v['vet_apellido']) ?></td>
                <td><span class="status-chip is-pendiente"><?= esc($v['fecha']) ?></span></td>
                <td><?= esc($v['dosis']) ?></td>
                <td>
                  <a href="<?= base_url('clinico/vacunas/eliminar/' . $v['id']) ?>"
                     onclick="return confirm('¿Eliminar esta vacuna programada?');"
                     style="color:#ef4444;">Eliminar</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="content-box" style="margin-top:20px;">
    <div class="box-head flex-between">
      <h4>Vacunas Aplicadas</h4>
      <span class="counter-badge"><?= count($aplicadas) ?> total</span>
    </div>

    <div class="table-container">
      <table class="styled-table">
        <thead>
          <tr>
            <th>Animal</th>
            <th>Vacuna</th>
            <th>Veterinario</th>
            <th>Fecha</th>
            <th>Dosis</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($aplicadas)): ?>
            <tr>
              <td colspan="6" class="empty-state">No hay vacunas aplicadas registradas todavía.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($aplicadas as $v): ?>
              <tr>
                <td><strong><?= esc($v['animal']) ?></strong></td>
                <td><?= esc($v['vacuna']) ?></td>
                <td><?= esc($v['vet_nombre'] . ' ' . $v['vet_apellido']) ?></td>
                <td><span class="status-chip is-completado"><?= esc($v['fecha']) ?></span></td>
                <td><?= esc($v['dosis']) ?></td>
                <td>
                  <a href="<?= base_url('clinico/vacunas/eliminar/' . $v['id']) ?>"
                     onclick="return confirm('¿Eliminar este registro de vacuna aplicada?');"
                     style="color:#ef4444;">Eliminar</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

<?= $this->include('templates/footer') ?>
