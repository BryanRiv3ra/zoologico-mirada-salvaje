<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($titulo) ? $titulo . ' · Mirada Salvaje' : 'Mirada Salvaje · Panel' ?></title>
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

<div class="app-shell">

  <aside class="sidebar">
    <div class="sidebar__brand">
      Mirada Salvaje
      <span>Panel de administración</span>
    </div>

    <ul class="sidebar__nav">
      <li>
        <a href="<?= base_url('/') ?>" class="<?= (uri_string() == '' || uri_string() == '/') ? 'is-active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 11.5 12 4l9 7.5"/><path d="M5 10v9a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1v-9"/></svg>
          Resumen
        </a>
      </li>
      <li>
        <a href="<?= base_url('limpieza') ?>" class="<?= (strpos(uri_string(), 'limpieza') === 0) ? 'is-active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4v9a5 5 0 0 0 5 5h1"/><path d="M9 4v9a5 5 0 0 1-5 5"/><path d="M15 3v7"/><path d="M12 6h6"/><circle cx="15" cy="18" r="3"/></svg>
          Limpieza
        </a>
      </li>
      <li>
        <a href="<?= base_url('alimentacion') ?>" class="<?= (strpos(uri_string(), 'alimentacion') === 0) ? 'is-active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2v6a3 3 0 0 0 3 3v11"/><path d="M6 2v9"/><path d="M9 2v9"/><path d="M18 2c-2 2-3 4-3 7a3 3 0 0 0 3 3v9"/></svg>
          Alimentación
        </a>
      </li>
      <li>
        <a href="<?= base_url('clinico') ?>" class="<?= (strpos(uri_string(), 'clinico') === 0) ? 'is-active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 3h6v4h4v6h-4v4H9v-4H5V7h4V3Z"/></svg>
          Control clínico
        </a>
      </li>
      <li>
        <a href="<?= base_url('entradas') ?>" class="<?= (strpos(uri_string(), 'entradas') === 0) ? 'is-active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M3 10h18"/><path d="M7 14h4"/></svg>
          Entradas y promos
        </a>
      </li>
    </ul>

    <div class="sidebar__foot">
      Proyecto Análisis de Sistemas II<br>
      Zoológico Mirada Salvaje
    </div>
  </aside>

  <main class="main">
