<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between" style="align-items:flex-start;">
    <div>
        <h1>Inventario de alimentos</h1>
        <p>Registro y consulta del stock actual de los alimentos disponibles para el módulo de alimentación.</p>
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
    <h2>Registrar alimento</h2>

    <form action="<?= base_url('alimentacion/inventario/guardar') ?>" method="post" class="modern-form">
        <div class="form-table-layout">
            <div class="form-group">
                <label for="nombre">Nombre del alimento</label>
                <input 
                    type="text" 
                    name="nombre" 
                    id="nombre" 
                    value="<?= old('nombre') ?>" 
                    placeholder="Ej: Banano, concentrado, zanahoria"
                    required
                >
            </div>

            <div class="form-group">
                <label for="unidad">Unidad</label>
                <input 
                    type="text" 
                    name="unidad" 
                    id="unidad" 
                    value="<?= old('unidad') ?>" 
                    placeholder="Ej: kg, unidad, libra"
                    required
                >
            </div>

            <div class="form-group">
                <label for="stock_actual">Stock actual</label>
                <input 
                    type="number" 
                    step="0.01" 
                    min="0" 
                    name="stock_actual" 
                    id="stock_actual" 
                    value="<?= old('stock_actual') ?>" 
                    placeholder="Ej: 50"
                    required
                >
            </div>

            <div class="form-group">
                <label for="stock_minimo">Stock mínimo</label>
                <input 
                    type="number" 
                    step="0.01" 
                    min="0" 
                    name="stock_minimo" 
                    id="stock_minimo" 
                    value="<?= old('stock_minimo') ?>" 
                    placeholder="Ej: 10"
                    required
                >
            </div>

            <div class="form-group">
                <label for="fecha_vencimiento">Fecha de vencimiento</label>
                <input 
                    type="date" 
                    name="fecha_vencimiento" 
                    id="fecha_vencimiento" 
                    value="<?= old('fecha_vencimiento') ?>"
                >
            </div>
        </div>

        <button type="submit" class="submit-btn">
            Guardar alimento
        </button>
    </form>
</div>

<div class="content-box">
    <div class="table-header">
        <h2>Resumen de stock</h2>
        <span class="status-chip"><?= count($alimentos) ?> alimentos</span>
    </div>

    <?php if (! empty($stockBajo)): ?>
        <div class="alert alert-danger">
            Hay <?= count($stockBajo) ?> alimento(s) con stock bajo o en el mínimo.
        </div>
    <?php else: ?>
        <div class="alert alert-success">
            No hay alimentos con stock bajo.
        </div>
    <?php endif; ?>

    <table class="styled-table">
        <thead>
            <tr>
                <th>Alimento</th>
                <th>Unidad</th>
                <th>Stock actual</th>
                <th>Stock mínimo</th>
                <th>Vencimiento</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($alimentos)): ?>
                <tr>
                    <td colspan="7" class="text-center">
                        No hay alimentos registrados en inventario.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($alimentos as $alimento): ?>
                    <?php
                        $stockActual = (float) $alimento['stock_actual'];
                        $stockMinimo = (float) $alimento['stock_minimo'];
                        $stockBajoItem = $stockActual <= $stockMinimo;
                    ?>

                    <tr>
                        <td><?= esc($alimento['nombre']) ?></td>
                        <td><?= esc($alimento['unidad']) ?></td>
                        <td><?= esc($alimento['stock_actual']) ?></td>
                        <td><?= esc($alimento['stock_minimo']) ?></td>
                        <td>
                            <?= ! empty($alimento['fecha_vencimiento']) 
                                ? esc(date('d/m/Y', strtotime($alimento['fecha_vencimiento']))) 
                                : 'Sin fecha' ?>
                        </td>
                        <td>
                            <?php if ($stockBajoItem): ?>
                                <span class="status-chip" style="background:#fee2e2;color:#991b1b;">
                                    Bajo stock
                                </span>
                            <?php else: ?>
                                <span class="status-chip">
                                    Suficiente
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a 
                                href="<?= base_url('alimentacion/inventario/eliminar/' . $alimento['id']) ?>" 
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('¿Eliminar este alimento?')"
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