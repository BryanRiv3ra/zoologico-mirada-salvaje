<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between" style="align-items:flex-start;">
  <div>
    <h2>Dietas por animal</h2>
    <p>Registro y consulta de dietas asignadas a los animales del zoológico.</p>
  </div>

  <a href="<?= base_url('alimentacion') ?>" class="submit-btn"
     style="display:inline-block;width:auto;white-space:nowrap;text-decoration:none;padding:11px 18px;">
    Volver al módulo
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
      <h4>Registrar dieta</h4>
    </div>

    <form method="post" action="<?= base_url('alimentacion/dietas/guardar') ?>" class="modern-form">

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
          <small style="color:var(--text-muted, #64748b)">
            No hay animales activos registrados todavía en <code>core.animales</code>.
          </small>
        <?php endif; ?>
      </div>

      <div class="form-field">
        <label for="descripcion">Descripción de la dieta</label>
        <textarea id="descripcion" name="descripcion" rows="3"
                  placeholder="Ej: Dieta balanceada con frutas, vegetales y suplemento especial."
                  required><?= old('descripcion') ?></textarea>
      </div>

      <div class="form-field">
        <label for="frecuencia">Frecuencia</label>
        <input type="text" id="frecuencia" name="frecuencia"
               placeholder="Ej: 2 veces al día"
               value="<?= old('frecuencia') ?>"
               required>
      </div>

      <button type="submit" class="submit-btn">Guardar dieta</button>
    </form>
  </div>

  <div class="content-box">
    <div class="box-head flex-between">
      <h4>Dietas registradas</h4>
      <span class="counter-badge"><?= count($dietas) ?> total</span>
    </div>

    <div class="table-container">
      <table class="styled-table">
        <thead>
          <tr>
            <th>Animal</th>
            <th>Descripción</th>
            <th>Frecuencia</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($dietas)): ?>
            <tr>
              <td colspan="4" class="empty-state">No hay dietas registradas todavía.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($dietas as $dieta): ?>
              <tr>
                <td><strong><?= esc($dieta['animal']) ?></strong></td>
                <td><?= esc($dieta['descripcion']) ?></td>
                <td>
                  <span class="status-chip is-pendiente">
                    <?= esc($dieta['frecuencia']) ?>
                  </span>
                </td>
                <td>
                  <a href="<?= base_url('alimentacion/dietas/eliminar/' . $dieta['id']) ?>"
                     onclick="return confirm('¿Eliminar esta dieta?');"
                     style="color:#ef4444;">
                    Eliminar
                  </a>
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