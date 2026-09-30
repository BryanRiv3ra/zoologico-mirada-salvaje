<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between">
  <div>
    <h2>Animales</h2>
    <p>Registro de la fauna del zoológico: especie, ubicación, sexo y estado.</p>
  </div>
  <div style="display:flex;gap:10px;">
    <a href="<?= base_url('animales/especies') ?>" class="btn-module btn-module--sm">Especies</a>
    <?php if ($puedeEditar): ?>
      <a href="<?= base_url('animales/nuevo') ?>" class="btn-module">+ Nuevo animal</a>
    <?php endif; ?>
  </div>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box" style="margin-bottom:20px;">
  <form method="get" action="<?= base_url('animales') ?>" class="filter-bar">
    <div class="form-field">
      <label for="busqueda">Buscar por nombre</label>
      <input type="text" id="busqueda" name="busqueda" maxlength="100" class="auto-submit"
             value="<?= esc($filtros['busqueda'] ?? '') ?>" placeholder="Ej: Simba">
    </div>

    <div class="form-field">
      <label for="especie_id">Especie</label>
      <select name="especie_id" id="especie_id" class="auto-submit">
        <option value="">Todas</option>
        <?php foreach ($especies as $especie): ?>
          <option value="<?= esc($especie['id']) ?>" <?= (string) ($filtros['especie_id'] ?? '') === (string) $especie['id'] ? 'selected' : '' ?>>
            <?= esc($especie['nombre_comun']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-field">
      <label for="zona_id">Zona</label>
      <select name="zona_id" id="zona_id" class="auto-submit">
        <option value="">Todas</option>
        <?php foreach ($zonas as $zona): ?>
          <option value="<?= esc($zona['id']) ?>" <?= (string) ($filtros['zona_id'] ?? '') === (string) $zona['id'] ? 'selected' : '' ?>>
            <?= esc($zona['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-field">
      <label for="estado">Estado</label>
      <select name="estado" id="estado" class="auto-submit">
        <option value="">Todos</option>
        <?php foreach ($estados as $valor => $etiqueta): ?>
          <option value="<?= esc($valor) ?>" <?= ($filtros['estado'] ?? '') === $valor ? 'selected' : '' ?>>
            <?= esc($etiqueta) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-field">
      <label>&nbsp;</label>
      <button type="submit" class="btn-module">Filtrar</button>
    </div>
  </form>
</div>

<div class="content-box">
  <div class="box-head flex-between">
    <h4>Animales registrados</h4>
    <span class="counter-badge"><?= count($animales) ?> resultado(s)</span>
  </div>

  <div class="table-container">
    <table class="styled-table">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Especie</th>
          <th>Zona</th>
          <th>Sexo</th>
          <th>Edad</th>
          <th>Estado</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($animales)): ?>
          <tr>
            <td colspan="7" class="empty-state">No hay animales que coincidan con los filtros seleccionados.</td>
          </tr>
        <?php else: ?>
          <?php
          $modelo = App\Models\AnimalModel::class;
          foreach ($animales as $animal):
              $edad = $modelo::edadLegible($animal['fecha_nacimiento']);
              $nombre = addslashes($animal['nombre']);
          ?>
            <tr>
              <td><strong><?= esc($animal['nombre']) ?></strong></td>
              <td><?= esc($animal['especie'] ?? '—') ?></td>
              <td><?= esc($animal['zona'] ?? '—') ?></td>
              <td>
                <span class="type-pill">
                  <?= ! empty($animal['sexo'])
                      ? esc($modelo::SEXOS[$animal['sexo']] ?? $animal['sexo'])
                      : 'No especificado' ?>
                </span>
              </td>
              <td><?= esc($edad ?? '—') ?></td>
              <td>
                <span class="status-chip <?= esc($modelo::claseEstado($animal['estado'])) ?>">
                  <?= esc($modelo::ESTADOS[$animal['estado']] ?? $animal['estado']) ?>
                </span>
              </td>
              <td class="td-actions">
                <?php if ($puedeEditar): ?>
                  <a href="<?= base_url('animales/editar/' . $animal['id']) ?>">Editar</a>
                  <?php if (! in_array($animal['estado'], $modelo::ESTADOS_BAJA, true)): ?>
                    <form method="post" action="<?= base_url('animales/baja/' . $animal['id']) ?>" class="inline-form"
                          onsubmit="return confirm('¿Dar de baja a <?= esc($nombre) ?>?\n\nSe conservarán su historial clínico, vacunas y dietas.');">
                      <?= csrf_field() ?>
                      <select name="estado" aria-label="Motivo de la baja" style="width:auto;padding:4px 8px;font-size:0.78rem;margin-right:6px;">
                        <option value="fallecido">Falleció</option>
                        <option value="trasladado">Trasladado</option>
                      </select>
                      <button type="submit" class="link-danger">Dar de baja</button>
                    </form>
                  <?php endif; ?>
                <?php else: ?>
                  <span style="color:var(--text-sub);font-size:0.8rem;">Solo lectura</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->include('templates/footer') ?>
