<?= $this->include('templates/header') ?>

  <div class="topbar">
    <div>
      <h1>Buen día</h1>
      <p>Este es el resumen general de operaciones del zoológico.</p>
    </div>
    <div class="topbar__meta">
      <strong><?= date('d/m/Y') ?></strong>
      Panel de administración
    </div>
  </div>

  <div class="module-grid">

    <a href="<?= base_url('limpieza') ?>" class="module-card module-card--wide" style="--accent: var(--color-limpieza)">
      <svg class="module-card__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 4v9a5 5 0 0 0 5 5h1"/><path d="M9 4v9a5 5 0 0 1-5 5"/><path d="M15 3v7"/><path d="M12 6h6"/><circle cx="15" cy="18" r="3"/></svg>
      <h2>Limpieza</h2>
      <p>Estado de jaulas, sanitarios, jardines, área de juegos y oficinas.</p>
      <div class="module-card__stats">
        <div class="module-card__stat">
          <b>—</b>
          <span>Áreas pendientes hoy</span>
        </div>
        <div class="module-card__stat">
          <b>—</b>
          <span>Completadas hoy</span>
        </div>
      </div>
      <span class="module-card__link">Ver módulo</span>
    </a>

    <a href="<?= base_url('entradas') ?>" class="module-card module-card--narrow" style="--accent: var(--color-entradas)">
      <svg class="module-card__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M3 10h18"/><path d="M7 14h4"/></svg>
      <h2>Entradas y promos</h2>
      <p>Venta de entradas y promociones vigentes.</p>
      <div class="module-card__stats">
        <div class="module-card__stat">
          <b>—</b>
          <span>Entradas hoy</span>
        </div>
      </div>
      <span class="module-card__link">Ver módulo</span>
    </a>

    <a href="<?= base_url('alimentacion') ?>" class="module-card module-card--half" style="--accent: var(--color-alimentacion)">
      <svg class="module-card__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 2v6a3 3 0 0 0 3 3v11"/><path d="M6 2v9"/><path d="M9 2v9"/><path d="M18 2c-2 2-3 4-3 7a3 3 0 0 0 3 3v9"/></svg>
      <h2>Alimentación</h2>
      <p>Horarios, dietas por animal e inventario de alimento.</p>
      <div class="module-card__stats">
        <div class="module-card__stat">
          <b>—</b>
          <span>Raciones hoy</span>
        </div>
        <div class="module-card__stat">
          <b>—</b>
          <span>Stock bajo</span>
        </div>
      </div>
      <span class="module-card__link">Ver módulo</span>
    </a>

    <a href="<?= base_url('clinico') ?>" class="module-card module-card--half" style="--accent: var(--color-clinico)">
      <svg class="module-card__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M9 3h6v4h4v6h-4v4H9v-4H5V7h4V3Z"/></svg>
      <h2>Control clínico</h2>
      <p>Medicamentos, vacunas y vitaminas por animal.</p>
      <div class="module-card__stats">
        <div class="module-card__stat">
          <b>—</b>
          <span>Vacunas próximas</span>
        </div>
        <div class="module-card__stat">
          <b>—</b>
          <span>En tratamiento</span>
        </div>
      </div>
      <span class="module-card__link">Ver módulo</span>
    </a>

  </div>

<?= $this->include('templates/footer') ?>
