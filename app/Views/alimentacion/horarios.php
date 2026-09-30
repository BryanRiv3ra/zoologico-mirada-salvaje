<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between" style="align-items:flex-start;">
    <div>
        <h1>Horarios de alimentación</h1>
        <p>Registro y consulta de horarios asignados a las dietas de los animales.</p>
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
    <h2>Registrar horario</h2>

    <form action="<?= base_url('alimentacion/horarios/guardar') ?>" method="post" class="modern-form">
        <div class="form-table-layout">
            <div class="form-group">
                <label for="dieta_id">Dieta</label>
                <select name="dieta_id" id="dieta_id" required>
                    <option value="">Seleccione una dieta</option>

                    <?php foreach ($dietas as $dieta): ?>
                        <option value="<?= esc($dieta['id']) ?>" <?= old('dieta_id') == $dieta['id'] ? 'selected' : '' ?>>
                            <?= esc($dieta['animal']) ?> - <?= esc($dieta['descripcion']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="hora">Hora de alimentación</label>
                <input 
                    type="time" 
                    name="hora" 
                    id="hora" 
                    value="<?= old('hora') ?>" 
                    required
                >
            </div>
        </div>

        <button type="submit" class="submit-btn">
            Guardar horario
        </button>
    </form>
</div>

<div class="content-box">
    <div class="table-header">
        <h2>Horarios registrados</h2>
        <span class="status-chip"><?= count($horarios) ?> total</span>
    </div>

    <table class="styled-table">
        <thead>
            <tr>
                <th>Animal</th>
                <th>Dieta</th>
                <th>Hora</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($horarios)): ?>
                <tr>
                    <td colspan="4" class="text-center">
                        No hay horarios registrados todavía.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($horarios as $horario): ?>
                    <tr>
                        <td><?= esc($horario['animal']) ?></td>
                        <td><?= esc($horario['descripcion']) ?></td>
                        <td>
                            <span class="status-chip">
                                <?= esc(substr($horario['hora'], 0, 5)) ?>
                            </span>
                        </td>
                        <td>
                            <a 
                                href="<?= base_url('alimentacion/horarios/eliminar/' . $horario['id']) ?>" 
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('¿Eliminar este horario?')"
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