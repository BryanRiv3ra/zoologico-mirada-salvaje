<?= $this->include('templates/header') ?>

<div class="page-title-row">
  <div>
    <h2><?= $modo === 'crear' ? 'Nueva Especie' : 'Editar Especie' ?></h2>
    <p><?= $modo === 'crear' ? 'Registra una especie del zoológico.' : 'Modifica los datos de la especie.' ?></p>
  </div>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box" style="max-width:640px;">
  <form method="post"
        action="<?= $modo === 'crear' ? base_url('animales/especies/guardar') : base_url('animales/especies/actualizar/' . $especie['id']) ?>">
    <?= csrf_field() ?>

    <div class="form-field">
      <label for="nombre_comun">Nombre común *</label>
      <input type="text" id="nombre_comun" name="nombre_comun" maxlength="100"
             value="<?= esc(old('nombre_comun', $especie['nombre_comun'])) ?>"
             placeholder="Ej: León africano" required>
    </div>

    <div class="form-field">
      <label for="nombre_cientifico">Nombre científico</label>
      <input type="text" id="nombre_cientifico" name="nombre_cientifico" maxlength="150"
             value="<?= esc(old('nombre_cientifico', $especie['nombre_cientifico'] ?? '')) ?>"
             placeholder="Ej: Panthera leo">
    </div>

    <div class="form-field">
      <label for="descripcion">Descripción</label>
      <textarea id="descripcion" name="descripcion" rows="4"
                placeholder="Ej: Felino de gran tamaño, distribuido en África subsahariana."><?= esc(old('descripcion', $especie['descripcion'] ?? '')) ?></textarea>
    </div>

    <button type="submit" class="submit-btn"><?= $modo === 'crear' ? 'Crear Especie' : 'Guardar Cambios' ?></button>
  </form>
</div>

<?= $this->include('templates/footer') ?>
