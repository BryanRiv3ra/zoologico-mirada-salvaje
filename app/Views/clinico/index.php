<?= $this->include('templates/header') ?>

      <div class="page-title-row flex-between" style="align-items:flex-start;">
    <div>
      <h2>Historial Clínico</h2>
      <p>Registro de tratamientos (medicamentos y vitaminas) por animal.</p>
    </div>
    <a href="<?= base_url('clinico/vacunas') ?>" class="submit-btn"
       style="display:inline-block;width:auto;white-space:nowrap;text-decoration:none;padding:11px 18px;">
      Ver calendario de vacunas
    </a>
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
        <h4>Registrar Tratamiento</h4>
      </div>

      <form method="post" action="<?= base_url('clinico/guardar') ?>" class="modern-form">

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
          <?php if (empty($animales)): ?>
            <small style="color:var(--text-muted, #64748b)">No hay animales registrados todavía en <code>core.animales</code>.</small>
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
          <label for="diagnostico">Diagnóstico</label>
          <textarea id="diagnostico" name="diagnostico" rows="2" placeholder="Ej: Infección respiratoria leve" required><?= old('diagnostico') ?></textarea>
        </div>

        <div class="form-field">
          <label for="inventario_id">Medicamento / Vitamina</label>
          <select id="inventario_id" name="inventario_id" required>
            <option value="">-- Selecciona un insumo --</option>
            <?php foreach ($insumos as $insumo): ?>
              <option value="<?= esc($insumo['id']) ?>" <?= old('inventario_id') == $insumo['id'] ? 'selected' : '' ?>>
                <?= esc($insumo['nombre']) ?> (<?= esc(ucfirst($insumo['tipo'])) ?> — stock: <?= esc($insumo['stock_actual']) ?> <?= esc($insumo['unidad']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (empty($insumos)): ?>
            <small style="color:var(--text-muted, #64748b)">No hay medicamentos ni vitaminas cargados en <code>core.inventario</code> todavía.</small>
          <?php endif; ?>
        </div>

        <div class="form-field">
          <label for="cantidad">Cantidad a descontar del inventario</label>
          <input type="number" step="0.01" id="cantidad" name="cantidad" placeholder="Ej: 2" value="<?= old('cantidad') ?>" required>
        </div>

        <div class="form-field">
          <label for="dosis">Dosis</label>
          <input type="text" id="dosis" name="dosis" placeholder="Ej: 5ml cada 12 horas" value="<?= old('dosis') ?>" required>
        </div>

        <div class="form-field">
          <label for="frecuencia">Frecuencia</label>
          <input type="text" id="frecuencia" name="frecuencia" placeholder="Ej: Cada 12 horas por 5 días" value="<?= old('frecuencia') ?>">
        </div>

        <div class="form-field">
          <label for="fecha_inicio">Fecha de inicio</label>
          <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?= old('fecha_inicio') ?>" required>
        </div>

        <div class="form-field">
          <label for="fecha_fin">Fecha de fin (opcional)</label>
          <input type="date" id="fecha_fin" name="fecha_fin" value="<?= old('fecha_fin') ?>">
        </div>

        <button type="submit" class="submit-btn">Guardar Tratamiento</button>
      </form>
    </div>

    <div class="content-box">
      <div class="box-head flex-between">
        <h4>Tratamientos Registrados</h4>
        <span class="counter-badge"><?= count($tratamientos) ?> total</span>
      </div>

      <div class="table-container">
        <table class="styled-table">
          <thead>
            <tr>
              <th>Animal</th>
              <th>Insumo</th>
              <th>Dosis</th>
              <th>Frecuencia</th>
              <th>Inicio</th>
              <th>Fin</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($tratamientos)): ?>
              <tr>
                <td colspan="7" class="empty-state">No hay tratamientos registrados todavía.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($tratamientos as $t): ?>
                <tr>
                  <td><strong><?= esc($t['animal']) ?></strong></td>
                  <td>
                    <span class="type-pill"><?= esc(ucfirst($t['tipo_insumo'])) ?></span>
                    <?= esc($t['insumo']) ?>
                  </td>
                  <td><?= esc($t['dosis']) ?></td>
                  <td><?= esc($t['frecuencia']) ?></td>
                  <td><?= esc($t['fecha_inicio']) ?></td>
                  <td>
                    <?php if ($t['fecha_fin']): ?>
                      <span class="status-chip is-completado"><?= esc($t['fecha_fin']) ?></span>
                    <?php else: ?>
                      <span class="status-chip is-pendiente">En curso</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <a href="<?= base_url('clinico/editar/' . $t['id']) ?>" style="margin-right:10px;">Editar</a>
                    <a href="<?= base_url('clinico/eliminar/' . $t['id']) ?>"
                       onclick="return confirm('¿Eliminar este tratamiento? El stock ya descontado no se restaura.');"
                       style="color:#ef4444;">Eliminar</a>
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
