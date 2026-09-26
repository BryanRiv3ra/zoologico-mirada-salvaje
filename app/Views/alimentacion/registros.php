<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between" style="align-items:flex-start;">
    <div>
        <h1>Registros de alimentación</h1>
        <p>Registro y consulta de alimentaciones realizadas según los horarios programados.</p>
    </div>

    <a href="<?= base_url('alimentacion') ?>" class="submit-btn" style="text-decoration:none;display:inline-block;width:auto;padding:12px 22px;">
        Volver al módulo
    </a>
</div>

<?php if (session()->getFlashdata('mensaje')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('mensaje') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errores')): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach (session()->getFlashdata('errores') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="content-box">
    <h2>Registrar alimentación realizada</h2>

    <form action="<?= base_url('alimentacion/registros/guardar') ?>" method="post" class="modern-form">
        <div class="form-table-layout">
            <div class="form-group">
                <label for="horario_id">Horario de alimentación</label>
                <select name="horario_id" id="horario_id" required>
                    <option value="">Seleccione un horario</option>

                    <?php foreach ($horarios as $horario): ?>
                        <option value="<?= esc($horario['id']) ?>" <?= old('horario_id') == $horario['id'] ? 'selected' : '' ?>>
                            <?= esc($horario['animal']) ?> - <?= esc($horario['descripcion']) ?> - <?= esc(substr($horario['hora'], 0, 5)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="empleado_id">Empleado responsable</label>
                <select name="empleado_id" id="empleado_id" required>
                    <option value="">Seleccione un empleado</option>

                    <?php foreach ($empleados as $empleado): ?>
                        <option value="<?= esc($empleado['id']) ?>" <?= old('empleado_id') == $empleado['id'] ? 'selected' : '' ?>>
                            <?= esc($empleado['nombre']) ?> <?= esc($empleado['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group" style="margin-top:16px;">
            <label for="observaciones" style="display:block;margin-bottom:8px;">
                Observaciones
            </label>
            <textarea 
                name="observaciones" 
                id="observaciones" 
                rows="3" 
                placeholder="Ej: Alimentación realizada sin inconvenientes."
                style="width:100%;min-height:90px;resize:vertical;"
            ><?= old('observaciones') ?></textarea>
        </div>

        <button type="submit" class="submit-btn">
            Guardar registro
        </button>
    </form>
</div>

<div class="content-box">
    <div class="table-header">
        <h2>Registros realizados</h2>
        <span class="status-chip"><?= count($registros) ?> total</span>
    </div>

    <table class="styled-table">
        <thead>
            <tr>
                <th>Animal</th>
                <th>Dieta</th>
                <th>Hora</th>
                <th>Empleado</th>
                <th>Fecha</th>
                <th>Observaciones</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($registros)): ?>
                <tr>
                    <td colspan="7" class="text-center">
                        No hay registros de alimentación todavía.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($registros as $registro): ?>
                    <tr>
                        <td><?= esc($registro['animal']) ?></td>
                        <td><?= esc($registro['descripcion']) ?></td>
                        <td>
                            <span class="status-chip">
                                <?= esc(substr($registro['hora'], 0, 5)) ?>
                            </span>
                        </td>
                        <td>
                            <?= esc($registro['empleado_nombre']) ?> <?= esc($registro['empleado_apellido']) ?>
                        </td>
                        <td><?= esc(date('d/m/Y H:i', strtotime($registro['fecha']))) ?></td>
                        <td><?= esc($registro['observaciones'] ?? 'Sin observaciones') ?></td>
                        <td>
                            <a 
                                href="<?= base_url('alimentacion/registros/eliminar/' . $registro['id']) ?>" 
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('¿Eliminar este registro?')"
                            >
                                Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->include('templates/footer') ?>