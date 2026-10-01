<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between">
  <div>
    <h2>Reporte de Limpieza</h2>
    <p>Cumplimiento de asignaciones por rango de fechas, zona y empleado.</p>
  </div>
  <div>
    <a href="<?= base_url('limpieza/reportes/exportar') ?>" class="btn-module">Exportar CSV</a>
    <a href="<?= base_url('limpieza/reportes/imprimir') ?>" class="btn-module">Imprimir</a>
  </div>
</div>

<?= $this->include('templates/alertas') ?>

<div class="content-box" style="margin-bottom:20px;">
  <form method="get" action="<?= base_url('limpieza/reportes') ?>" class="filter-row">
    <div class="form-field">
      <label for="desde">Desde</label>
      <input type="date" id="desde" name="desde" value="<?= esc($filtros['desde']) ?>">
    </div>
    <div class="form-field">
      <label for="hasta">Hasta</label>
      <input type="date" id="hasta" name="hasta" value="<?= esc($filtros['hasta']) ?>">
    </div>
    <div class="form-field">
      <label for="zona">Zona</label>
      <select id="zona" name="zona">
        <option value="0">Todas</option>
        <?php foreach ($zonas as $zona): ?>
          <option value="<?= esc($zona['id']) ?>" <?= (int) $filtros['zona'] === (int) $zona['id'] ? 'selected' : '' ?>><?= esc($zona['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-field">
      <label for="tipo">Tipo de zona</label>
      <select id="tipo" name="tipo">
        <option value="">Todos</option>
        <option value="jaula" <?= $filtros['tipo'] === 'jaula' ? 'selected' : '' ?>>Recinto / Jaula</option>
        <option value="sanitario" <?= $filtros['tipo'] === 'sanitario' ? 'selected' : '' ?>>Servicio Sanitario</option>
        <option value="jardin" <?= $filtros['tipo'] === 'jardin' ? 'selected' : '' ?>>Sendero / Jardín</option>
        <option value="area_juegos" <?= $filtros['tipo'] === 'area_juegos' ? 'selected' : '' ?>>Área Recreativa</option>
        <option value="oficina" <?= $filtros['tipo'] === 'oficina' ? 'selected' : '' ?>>Oficina Administrativa</option>
      </select>
    </div>
    <div class="form-field">
      <label for="empleado">Empleado</label>
      <select id="empleado" name="empleado">
        <option value="0">Todos</option>
        <?php foreach ($empleados as $empleado): ?>
          <option value="<?= esc($empleado['id']) ?>" <?= (int) $filtros['empleado'] === (int) $empleado['id'] ? 'selected' : '' ?>>
            <?= esc($empleado['nombre'] . ' ' . ($empleado['apellido'] ?? '')) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="submit-btn" style="align-self:flex-end;">Generar</button>
  </form>
</div>

<div class="stat-row">
  <div class="stat-pill">
    <span class="stat-pill__label">Asignadas</span>
    <strong class="stat-pill__val"><?= $totales['asignadas'] ?></strong>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Listas</span>
    <strong class="stat-pill__val"><?= $totales['listas'] ?></strong>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">En curso</span>
    <strong class="stat-pill__val"><?= $totales['en_curso'] ?></strong>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Pendientes</span>
    <strong class="stat-pill__val"><?= $totales['pendientes'] ?></strong>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Vencidas</span>
    <strong class="stat-pill__val" style="color:var(--color-coral);"><?= $totales['vencidas'] ?></strong>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Cumplimiento</span>
    <strong class="stat-pill__val"><?= $totales['cumplimiento'] ?>%</strong>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Duración prom.</span>
    <strong class="stat-pill__val"><?= esc($duracionPromedio) ?></strong>
  </div>
</div>

<div class="form-table-layout" style="margin-top:20px;">
  <div class="content-box">
    <div class="box-head">
      <h4>Cumplimiento por zona</h4>
    </div>
    <div class="table-container">
      <table class="styled-table">
        <thead>
          <tr><th>Zona</th><th>Asignadas</th><th>Listas</th><th>Cumplimiento</th></tr>
        </thead>
        <tbody>
          <?php if (empty($porZona)): ?>
            <tr><td colspan="4" class="empty-state">Sin datos en el rango.</td></tr>
          <?php else: ?>
            <?php foreach ($porZona as $fila): ?>
              <tr>
                <td><?= esc($fila['nombre']) ?></td>
                <td><?= $fila['asignadas'] ?></td>
                <td><?= $fila['listas'] ?></td>
                <td><?= $fila['cumplimiento'] ?>%</td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="content-box">
    <div class="box-head">
      <h4>Cumplimiento por empleado</h4>
    </div>
    <div class="table-container">
      <table class="styled-table">
        <thead>
          <tr><th>Empleado</th><th>Asignadas</th><th>Listas</th><th>Cumplimiento</th></tr>
        </thead>
        <tbody>
          <?php if (empty($porEmpleado)): ?>
            <tr><td colspan="4" class="empty-state">Sin datos en el rango.</td></tr>
          <?php else: ?>
            <?php foreach ($porEmpleado as $fila): ?>
              <tr>
                <td><?= esc($fila['nombre']) ?></td>
                <td><?= $fila['asignadas'] ?></td>
                <td><?= $fila['listas'] ?></td>
                <td><?= $fila['cumplimiento'] ?>%</td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?= $this->include('templates/footer') ?>