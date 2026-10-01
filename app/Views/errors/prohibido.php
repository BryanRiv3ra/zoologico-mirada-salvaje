<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sin permiso · Mirada Salvaje</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body style="display:flex;align-items:center;justify-content:center;min-height:100vh;background:var(--bg-app);">

  <div class="content-box" style="max-width:420px;width:100%;text-align:center;">
    <div class="brand-badge" style="border-bottom:none;justify-content:center;padding-bottom:8px;">
      <div class="brand-badge__icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a9 9 0 0 1 9 9c0 4.97-4.03 9-9 9s-9-4.03-9-9a9 9 0 0 1 9-9Z"/><path d="M12 6v6l4 2"/></svg>
      </div>
      <div class="brand-badge__text">
        <span>MIRADA SALVAJE</span>
        <small>Sistema de Control</small>
      </div>
    </div>

    <div style="font-family:var(--font-title);font-size:3rem;font-weight:700;color:var(--color-coral);margin:12px 0 0;">403</div>
    <h3 style="font-family:var(--font-title);margin:0 0 8px;">Acceso restringido</h3>
    <p style="color:var(--text-muted);font-size:0.92rem;line-height:1.5;">
      <?= esc($nombre ?? 'El usuario') ?> con rol(es) <strong><?= esc(implode(', ', $roles ?? [])) ?></strong>
      no tiene permisos para esta sección. Se requiere: <strong><?= esc(implode(', ', $rolesOk ?? [])) ?></strong>.
    </p>

    <form method="post" action="<?= base_url('logout') ?>" style="margin-top:18px;">
      <?= csrf_field() ?>
      <button type="submit" class="submit-btn">Regresar al panel</button>
    </form>
  </div>

</body>
</html>