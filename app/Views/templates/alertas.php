<?php
// Mensajes flash (success / error) y errores de validaci├│n.
$erroresValidacion = session('errors') ?? [];
$alertaError       = session('error') ?? null;
$alertaExito       = session('success') ?? null;
?>
<?php if ($alertaExito !== null): ?>
  <div class="alert-box is-success"><?= esc($alertaExito) ?></div>
<?php endif; ?>

<?php if ($alertaError !== null): ?>
  <div class="alert-box is-error"><?= esc($alertaError) ?></div>
<?php endif; ?>

<?php if ($erroresValidacion !== []): ?>
  <div class="alert-box is-error">
    <strong>Revisa los campos marcados:</strong>
    <ul>
      <?php foreach ($erroresValidacion as $mensaje): ?>
        <li><?= esc(is_array($mensaje) ? implode(', ', $mensaje) : $mensaje) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>