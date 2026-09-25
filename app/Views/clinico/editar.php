<?= $this->include('templates/header') ?>

  <div class="page-title-row">
    <div>
      <h2>Editar Tratamiento</h2>
      <p>Puedes ajustar dosis, frecuencia o cerrar el tratamiento con una fecha de fin.</p>
    </div>
  </div>

  <?php if (session()->getFlashdata('errores')): ?>
    <div class="content-box" style="border-left:4px solid #ef4444;margin-bottom:16px;">
      <strong>No se pudo actualizar:</strong>
      <ul style="margin:8px 0 0 18px;">
        <?php foreach (session()->getFlashdata('errores') as $error): ?>
          <li><?= esc($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="content-box" style="max-width:420px;">
    <div class="box-head">
      <h4>Datos del Tratamiento #<?= esc($tratamiento['id']) ?></h4>
    </div>

    <form method="post" action="<?= base_url('clinico/actualizar/' . $tratamiento['id']) ?>" class="modern-form">

      <div class="form-field">
        <label for="dosis">Dosis</label>
        <input type="text" id="dosis" name="dosis" value="<?= old('dosis', $tratamiento['dosis']) ?>" required>
      </div>

      <div class="form-field">
        <label for="frecuencia">Frecuencia</label>
        <input type="text" id="frecuencia" name="frecuencia" value="<?= old('frecuencia', $tratamiento['frecuencia']) ?>">
      </div>

      <div class="form-field">
        <label for="fecha_fin">Fecha de fin</label>
        <input type="date" id="fecha_fin" name="fecha_fin" value="<?= old('fecha_fin', $tratamiento['fecha_fin']) ?>">
      </div>

      <button type="submit" class="submit-btn">Actualizar</button>
      <a href="<?= base_url('clinico') ?>" style="display:block;text-align:center;margin-top:10px;">Cancelar</a>
    </form>
  </div>

<?= $this->include('templates/footer') ?>
