<?= $this->include('templates/header') ?>

<div class="page-title-row">
  <div>
    <h2><?= $modo === 'crear' ? 'Nuevo Animal' : 'Editar Animal' ?></h2>
    <p><?= $modo === 'crear' ? 'Registra un animal del zoológico.' : 'Modifica los datos del animal.' ?></p>
  </div>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box" style="max-width:720px;">
  <?php if ($modo === 'crear' && empty($especies)): ?>
    <div class="alert-box is-error">
      No hay especies registradas. Crea primero una especie en
      <a href="<?= base_url('animales/especies/nueva') ?>">Animales &rsaquo; Especies</a>.
    </div>
  <?php else: ?>
    <form method="post"
          action="<?= $modo === 'crear' ? base_url('animales/guardar') : base_url('animales/actualizar/' . $animal['id']) ?>">
      <?= csrf_field() ?>

      <div class="form-field">
        <label for="nombre">Nombre del animal *</label>
        <input type="text" id="nombre" name="nombre" maxlength="100"
               value="<?= esc(old('nombre', $animal['nombre'])) ?>"
               placeholder="Ej: Simba" required>
      </div>

      <div class="form-grid">
        <div class="form-field">
          <label for="especie_id">Especie *</label>
          <select id="especie_id" name="especie_id" required>
            <option value="">Selecciona una especie</option>
            <?php foreach ($especies as $especie): ?>
              <option value="<?= esc($especie['id']) ?>" <?= (string) old('especie_id', $animal['especie_id']) === (string) $especie['id'] ? 'selected' : '' ?>>
                <?= esc($especie['nombre_comun']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-field">
          <label for="zona_id">Zona</label>
          <select id="zona_id" name="zona_id">
            <option value="">Sin asignar</option>
            <?php foreach ($zonas as $zona): ?>
              <option value="<?= esc($zona['id']) ?>" <?= (string) old('zona_id', $animal['zona_id']) === (string) $zona['id'] ? 'selected' : '' ?>>
                <?= esc($zona['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-field">
          <label for="sexo">Sexo</label>
          <select id="sexo" name="sexo">
            <option value="">No especificado</option>
            <?php foreach ($sexos as $valor => $etiqueta): ?>
              <option value="<?= esc($valor) ?>" <?= old('sexo', $animal['sexo']) === $valor ? 'selected' : '' ?>>
                <?= esc($etiqueta) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-field">
          <label for="fecha_nacimiento">Fecha de nacimiento</label>
          <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" max="<?= esc(date('Y-m-d')) ?>"
                 value="<?= esc(old('fecha_nacimiento', $animal['fecha_nacimiento'] ?? '')) ?>">
        </div>

        <div class="form-field form-grid--full">
          <label for="estado">Estado *</label>
          <select id="estado" name="estado" required>
            <?php foreach (App\Models\AnimalModel::ESTADOS as $valor => $etiqueta): ?>
              <option value="<?= esc($valor) ?>" <?= old('estado', $animal['estado']) === $valor ? 'selected' : '' ?>>
                <?= esc($etiqueta) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="info-box">
        La baja de un animal <strong>no borra la fila</strong>: se cambia su estado a
        «Fallecido» o «Trasladado» para conservar el historial clínico, las vacunas
        y las dietas asociadas.
      </div>

      <button type="submit" class="submit-btn"><?= $modo === 'crear' ? 'Registrar Animal' : 'Guardar Cambios' ?></button>
    </form>
  <?php endif; ?>
</div>

<?= $this->include('templates/footer') ?>
