<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($titulo) ? esc($titulo) . ' · Mirada Salvaje' : 'Mirada Salvaje · Control' ?></title>
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
  <?php if (! empty($cssExtra)): ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/' . esc($cssExtra)) ?>">
  <?php endif; ?>
</head>
<body>

<div class="layout-wrapper">

  <!-- Sidebar Lateral -->
  <aside class="nav-sidebar">
    <div class="brand-badge">
      <div class="brand-badge__icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a9 9 0 0 1 9 9c0 4.97-4.03 9-9 9s-9-4.03-9-9a9 9 0 0 1 9-9Z"/><path d="M12 6v6l4 2"/></svg>
      </div>
      <div class="brand-badge__text">
        <span>MIRADA SALVAJE</span>
        <small>Sistema de Control</small>
      </div>
    </div>

    <div class="nav-category">OPERACIONES</div>
    <ul class="nav-menu">
      <li>
        <a href="<?= base_url('/') ?>" class="<?= (uri_string() == '' || uri_string() == '/') ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/></svg>
          Panel General
        </a>
      </li>
      <li>
        <a href="<?= base_url('limpieza') ?>" class="<?= (strpos(uri_string(), 'limpieza') === 0) ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4v9a5 5 0 0 0 5 5h1"/><path d="M9 4v9a5 5 0 0 1-5 5"/><path d="M15 3v7"/><path d="M12 6h6"/><circle cx="15" cy="18" r="3"/></svg>
          Limpieza y Hábitats
        </a>
      </li>
      <li>
        <a href="<?= base_url('alimentacion') ?>" class="<?= (strpos(uri_string(), 'alimentacion') === 0) ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2v6a3 3 0 0 0 3 3v11"/><path d="M6 2v9"/><path d="M9 2v9"/><path d="M18 2c-2 2-3 4-3 7a3 3 0 0 0 3 3v9"/></svg>
          Dietas y Alimentos
        </a>
      </li>
      <li>
        <a href="<?= base_url('clinico') ?>" class="<?= (strpos(uri_string(), 'clinico') === 0) ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
          Historial Clínico
        </a>
      </li>
      <li>
        <a href="<?= base_url('entradas') ?>" class="<?= (strpos(uri_string(), 'entradas') === 0) ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
          Taquilla & Promos
        </a>
      </li>
    </ul>

    <div class="nav-footer">
      <div class="system-status-indicator">
        <span class="pulse-dot"></span>
        <span>Operaciones en línea</span>
      </div>
      <p>Análisis de Sistemas II</p>
    </div>
  </aside>

  <!-- Área Central de Trabajo -->
  <div class="content-container">
    <header class="top-nav">
      <div class="global-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Buscar hábitat, animal, personal...">
      </div>

      <div class="top-nav-actions">
        <div class="date-chip">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          <?= date('d M, Y') ?>
        </div>
        <div class="user-avatar-pill">
          <span class="avatar-letter">A</span>
          <span>Admin</span>
        </div>
      </div>
    </header>

    <main class="page-content">