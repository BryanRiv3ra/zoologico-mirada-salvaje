<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($titulo) ? esc($titulo) . ' · Mirada Salvaje' : 'Iniciar sesión · Mirada Salvaje' ?></title>
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="login-page">

  <div class="login-card">

    <!-- Identidad: mismo bloque que el sidebar, pero sobre fondo claro -->
    <div class="login-card__brand">
      <div class="brand-badge__icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a9 9 0 0 1 9 9c0 4.97-4.03 9-9 9s-9-4.03-9-9a9 9 0 0 1 9-9Z"/><path d="M12 6v6l4 2"/></svg>
      </div>
      <div class="brand-badge__text">
        <span>MIRADA SALVAJE</span>
        <small>Sistema de Control</small>
      </div>
    </div>

    <h1 class="login-card__title">Iniciar sesión</h1>
    <p class="login-card__sub">Ingresa con las credenciales que te asignó el administrador del zoológico.</p>

    <!-- Flash de error / éxito y errores de validación -->
    <?= $this->include('templates/alertas') ?>

    <form method="post" action="<?= base_url('login') ?>" novalidate>
      <?= csrf_field() ?>

      <?php
      // withInput() devuelve lo que se envió tal cual. Si alguien manda
      // "email[]=..." old('email') es un array y esc() reventaría con
      // "Array to string conversion", así que solo se pinta si es cadena.
      $emailPrevio = old('email');
      ?>
      <div class="form-field">
        <label for="email">Correo electrónico</label>
        <input
          type="email"
          name="email"
          id="email"
          value="<?= esc(is_string($emailPrevio) ? $emailPrevio : '') ?>"
          autocomplete="username"
          autofocus
          required>
      </div>

      <div class="form-field">
        <label for="password">Contraseña</label>
        <input
          type="password"
          name="password"
          id="password"
          autocomplete="current-password"
          required>
      </div>

      <button type="submit" class="submit-btn">Entrar</button>
    </form>

    <p class="login-card__foot">Mirada Salvaje · Análisis de Sistemas II</p>
  </div>

</body>
</html>
