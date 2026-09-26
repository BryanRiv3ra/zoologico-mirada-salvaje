<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between" style="align-items:flex-start;">
  <div>
    <h2>Módulo de Alimentación</h2>
    <p>Gestión de dietas, horarios, registros de alimentación e inventario de alimentos.</p>
  </div>
</div>

<div class="form-table-layout">
  <div class="content-box">
    <div class="box-head">
      <h4>Opciones del módulo</h4>
    </div>

<div style="display:grid;gap:14px;">
    <a href="<?= base_url('alimentacion/dietas') ?>" class="submit-btn" style="display:inline-block;text-align:center;text-decoration:none;">
        Dietas por animal
    </a>

    <a href="<?= base_url('alimentacion/horarios') ?>" class="submit-btn" style="display:inline-block;text-align:center;text-decoration:none;">
        Horarios de alimentación
    </a>

    <a href="<?= base_url('alimentacion/registros') ?>" class="submit-btn" style="display:inline-block;text-align:center;text-decoration:none;">
        Registros de alimentación
    </a>

    <a href="#" class="submit-btn" style="display:inline-block;text-align:center;text-decoration:none;">
        Inventario de alimentos
    </a>
</div>
  </div>

  <div class="content-box">
    <div class="box-head">
      <h4>Resumen del módulo</h4>
    </div>

    <div class="table-container">
      <table class="styled-table">
        <thead>
          <tr>
            <th>Área</th>
            <th>Descripción</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>Dietas</strong></td>
            <td>Registro de dietas asignadas a cada animal.</td>
            <td><span class="status-chip is-pendiente">Pendiente</span></td>
          </tr>
          <tr>
            <td><strong>Horarios</strong></td>
            <td>Programación de horarios de alimentación.</td>
            <td><span class="status-chip is-pendiente">Pendiente</span></td>
          </tr>
          <tr>
            <td><strong>Registros</strong></td>
            <td>Control de alimentaciones realizadas por empleados.</td>
            <td><span class="status-chip is-pendiente">Pendiente</span></td>
          </tr>
          <tr>
            <td><strong>Inventario</strong></td>
            <td>Consulta y control de stock de alimentos.</td>
            <td><span class="status-chip is-pendiente">Pendiente</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?= $this->include('templates/footer') ?>