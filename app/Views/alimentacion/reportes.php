<?= $this->include('templates/header') ?>

<div class="page-title-row flex-between" style="align-items:flex-start;">
    <div>
        <h1>Reporte de alimentación</h1>
        <p>Resumen general del inventario de alimentos y registros de alimentación realizados.</p>
    </div>

<div style="display:flex;gap:10px;">
    <button 
        type="button" 
        class="submit-btn" 
        style="width:auto;padding:12px 22px;"
        onclick="window.print()"
    >
        Imprimir / Guardar PDF
    </button>

    <a href="<?= base_url('alimentacion') ?>" class="submit-btn" style="text-decoration:none;display:inline-block;width:auto;padding:12px 22px;">
        Volver al módulo
    </a>
</div>

</div>

<div class="form-table-layout">
    <div class="content-box">
        <h2>Total de alimentos</h2>
        <p style="font-size:32px;font-weight:bold;margin:0;">
            <?= esc($totalAlimentos) ?>
        </p>
        <p>Alimentos registrados en inventario.</p>
    </div>

    <div class="content-box">
        <h2>Stock bajo</h2>
        <p style="font-size:32px;font-weight:bold;margin:0;">
            <?= esc($totalStockBajo) ?>
        </p>
        <p>Alimentos en mínimo o bajo stock.</p>
    </div>

    <div class="content-box">
        <h2>Registros realizados</h2>
        <p style="font-size:32px;font-weight:bold;margin:0;">
            <?= esc($totalRegistros) ?>
        </p>
        <p>Alimentaciones registradas en el sistema.</p>
    </div>
</div>

<div class="content-box">
    <div class="table-header">
        <h2>Alimentos con stock bajo</h2>
        <span class="status-chip"><?= count($stockBajo) ?> total</span>
    </div>

    <table class="styled-table">
        <thead>
            <tr>
                <th>Alimento</th>
                <th>Unidad</th>
                <th>Stock actual</th>
                <th>Stock mínimo</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($stockBajo)): ?>
                <tr>
                    <td colspan="5" class="text-center">
                        No hay alimentos con stock bajo.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($stockBajo as $alimento): ?>
                    <tr>
                        <td><?= esc($alimento['nombre']) ?></td>
                        <td><?= esc($alimento['unidad']) ?></td>
                        <td><?= esc($alimento['stock_actual']) ?></td>
                        <td><?= esc($alimento['stock_minimo']) ?></td>
                        <td>
                            <span class="status-chip" style="background:#fee2e2;color:#991b1b;">
                                Bajo stock
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="content-box">
    <div class="table-header">
        <h2>Inventario de alimentos</h2>
        <span class="status-chip"><?= count($alimentos) ?> alimentos</span>
    </div>

    <table class="styled-table">
        <thead>
            <tr>
                <th>Alimento</th>
                <th>Unidad</th>
                <th>Stock actual</th>
                <th>Stock mínimo</th>
                <th>Vencimiento</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($alimentos)): ?>
                <tr>
                    <td colspan="6" class="text-center">
                        No hay alimentos registrados.
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
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="content-box">
    <div class="table-header">
        <h2>Registros de alimentación recientes</h2>
        <span class="status-chip"><?= count($registros) ?> registros</span>
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
            </tr>
        </thead>

        <tbody>
            <?php if (empty($registros)): ?>
                <tr>
                    <td colspan="6" class="text-center">
                        No hay registros de alimentación todavía.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($registros as $registro): ?>
                    <tr>
                        <td><?= esc($registro['animal']) ?></td>
                        <td><?= esc($registro['descripcion']) ?></td>
                        <td><?= esc(substr($registro['hora'], 0, 5)) ?></td>
                        <td>
                            <?= esc($registro['empleado_nombre']) ?> <?= esc($registro['empleado_apellido']) ?>
                        </td>
                        <td><?= esc(date('d/m/Y H:i', strtotime($registro['fecha']))) ?></td>
                        <td><?= esc($registro['observaciones'] ?? 'Sin observaciones') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->include('templates/footer') ?>