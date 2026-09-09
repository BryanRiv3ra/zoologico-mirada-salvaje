<?= $this->include('templates/header') ?>

  <!-- Encabezado de Página -->
  <section class="banner-hero">
    <div class="banner-hero__text">
      <h2>Centro de Mando</h2>
      <p>Monitoreo en tiempo real de recintos, alimentación y taquilla.</p>
    </div>
    <div class="banner-hero__stats">
      <div class="stat-pill">
        <span class="stat-pill__label">Zonas Operativas</span>
        <strong class="stat-pill__val">12 / 12</strong>
      </div>
      <div class="stat-pill">
        <span class="stat-pill__label">Estado General</span>
        <strong class="stat-pill__val status-good">Normal</strong>
      </div>
    </div>
  </section>

  <!-- Grilla de Módulos -->
  <div class="cards-grid">

    <!-- Módulo 1: Limpieza -->
    <a href="<?= base_url('limpieza') ?>" class="action-card color-aqua">
      <div class="action-card__head">
        <div class="action-card__icon-box">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4v9a5 5 0 0 0 5 5h1"/><path d="M9 4v9a5 5 0 0 1-5 5"/><path d="M15 3v7"/><path d="M12 6h6"/><circle cx="15" cy="18" r="3"/></svg>
        </div>
        <span class="badge-tag">Hábitats</span>
      </div>
      <div class="action-card__body">
        <h3>Limpieza y Mantenimiento</h3>
        <p>Estado sanitario de jaulas, jardines, senderos y zonas comunes.</p>
      </div>
      <div class="action-card__metrics">
        <div class="metric-item">
          <b>—</b>
          <span>Pendientes</span>
        </div>
        <div class="metric-item">
          <b>—</b>
          <span>Completadas</span>
        </div>
      </div>
      <div class="action-card__cta">
        <span>Abrir registro</span>
        <span class="arrow-icon">→</span>
      </div>
    </a>

    <!-- Módulo 2: Entradas -->
    <a href="<?= base_url('entradas') ?>" class="action-card color-emerald">
      <div class="action-card__head">
        <div class="action-card__icon-box">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M3 10h18"/><path d="M7 14h4"/></svg>
        </div>
        <span class="badge-tag">Taquilla</span>
      </div>
      <div class="action-card__body">
        <h3>Entradas & Promociones</h3>
        <p>Control de boletaje vendido, acceso de visitantes y promociones activas.</p>
      </div>
      <div class="action-card__metrics">
        <div class="metric-item">
          <b>—</b>
          <span>Entradas del día</span>
        </div>
        <div class="metric-item">
          <b>Activas</b>
          <span>Promociones</span>
        </div>
      </div>
      <div class="action-card__cta">
        <span>Gestionar ventas</span>
        <span class="arrow-icon">→</span>
      </div>
    </a>

    <!-- Módulo 3: Alimentación -->
    <a href="<?= base_url('alimentacion') ?>" class="action-card color-amber">
      <div class="action-card__head">
        <div class="action-card__icon-box">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2v6a3 3 0 0 0 3 3v11"/><path d="M6 2v9"/><path d="M9 2v9"/><path d="M18 2c-2 2-3 4-3 7a3 3 0 0 0 3 3v9"/></svg>
        </div>
        <span class="badge-tag">Nutrición</span>
      </div>
      <div class="action-card__body">
        <h3>Alimentación y Dietas</h3>
        <p>Programación de turnos de comida, tipos de ración y stock de bodegas.</p>
      </div>
      <div class="action-card__metrics">
        <div class="metric-item">
          <b>—</b>
          <span>Raciones asignadas</span>
        </div>
        <div class="metric-item">
          <b>—</b>
          <span>Bajo stock</span>
        </div>
      </div>
      <div class="action-card__cta">
        <span>Ver dietas</span>
        <span class="arrow-icon">→</span>
      </div>
    </a>

    <!-- Módulo 4: Control Clínico -->
    <a href="<?= base_url('clinico') ?>" class="action-card color-coral">
      <div class="action-card__head">
        <div class="action-card__icon-box">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
        </div>
        <span class="badge-tag">Veterinaria</span>
      </div>
      <div class="action-card__body">
        <h3>Atención Veterinaria</h3>
        <p>Revisiones periódicas, medicamentos, vacunas y fichas de tratamiento.</p>
      </div>
      <div class="action-card__metrics">
        <div class="metric-item">
          <b>—</b>
          <span>Vacunas por aplicar</span>
        </div>
        <div class="metric-item">
          <b>—</b>
          <span>En observación</span>
        </div>
      </div>
      <div class="action-card__cta">
        <span>Fichas médicas</span>
        <span class="arrow-icon">→</span>
      </div>
    </a>

  </div>

<?= $this->include('templates/footer') ?>