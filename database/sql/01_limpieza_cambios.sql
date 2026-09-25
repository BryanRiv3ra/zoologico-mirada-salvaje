-- ============================================================================
-- MÓDULO DE LIMPIEZA — cambios sobre el DDL original (versión actualizada)
-- Idempotente: seguro de ejecutar varias veces (IF NOT EXISTS / additions).
-- ⚠️ No ejecutar contra Supabase compartida sin aprobación del equipo.
-- ============================================================================

-- Asegurar el esquema de trabajo.
CREATE SCHEMA IF NOT EXISTS limpieza;

-- ----------------------------------------------------------------------------
-- core.zonas: agregar columna activo (D1: Desactivar zona / Zona.desactivar()).
-- Tabla compartida con Alimentación (animales.zona_id): solo se agrega columna.
-- ----------------------------------------------------------------------------
ALTER TABLE core.zonas ADD COLUMN IF NOT EXISTS activo BOOLEAN NOT NULL DEFAULT TRUE;

-- ----------------------------------------------------------------------------
-- limpieza.tareas_limpieza: agregar activo y UNIQUE (zona_id, descripcion).
-- ----------------------------------------------------------------------------
ALTER TABLE limpieza.tareas_limpieza ADD COLUMN IF NOT EXISTS activo BOOLEAN NOT NULL DEFAULT TRUE;

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'tareas_limpieza_zona_desc_uniq'
          AND conrelid = 'limpieza.tareas_limpieza'::regclass
    ) THEN
        ALTER TABLE limpieza.tareas_limpieza
            ADD CONSTRAINT tareas_limpieza_zona_desc_uniq UNIQUE (zona_id, descripcion);
    END IF;
END $$;

-- ----------------------------------------------------------------------------
-- limpieza.tarea_insumo (nueva): tarea <-> insumo de core.inventario.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS limpieza.tarea_insumo (
    id            SERIAL PRIMARY KEY,
    tarea_id      INT NOT NULL REFERENCES limpieza.tareas_limpieza(id),
    inventario_id INT NOT NULL REFERENCES core.inventario(id),
    cantidad      NUMERIC(10,2) NOT NULL CHECK (cantidad > 0)
);

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'tarea_insumo_tarea_inventario_uniq'
          AND conrelid = 'limpieza.tarea_insumo'::regclass
    ) THEN
        ALTER TABLE limpieza.tarea_insumo
            ADD CONSTRAINT tarea_insumo_tarea_inventario_uniq UNIQUE (tarea_id, inventario_id);
    END IF;
END $$;

-- ----------------------------------------------------------------------------
-- limpieza.asignaciones_limpieza (nueva).
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS limpieza.asignaciones_limpieza (
    id              SERIAL PRIMARY KEY,
    tarea_id        INT NOT NULL REFERENCES limpieza.tareas_limpieza(id),
    empleado_id     INT NOT NULL REFERENCES core.empleados(id),
    asignado_por    INT NOT NULL REFERENCES core.usuarios(id),
    fecha_programada DATE NOT NULL,
    estado          VARCHAR(15) NOT NULL DEFAULT 'pendiente'
                    CHECK (estado IN ('pendiente','en_curso','listo')),
    inicio_real     TIMESTAMP,
    fin_real        TIMESTAMP
);

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'asignaciones_tarea_fecha_uniq'
          AND conrelid = 'limpieza.asignaciones_limpieza'::regclass
    ) THEN
        ALTER TABLE limpieza.asignaciones_limpieza
            ADD CONSTRAINT asignaciones_tarea_fecha_uniq UNIQUE (tarea_id, fecha_programada);
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'asignaciones_fin_mayor_inicio'
          AND conrelid = 'limpieza.asignaciones_limpieza'::regclass
    ) THEN
        ALTER TABLE limpieza.asignaciones_limpieza
            ADD CONSTRAINT asignaciones_fin_mayor_inicio
            CHECK (fin_real IS NULL OR inicio_real IS NULL OR fin_real >= inicio_real);
    END IF;
END $$;

CREATE INDEX IF NOT EXISTS idx_asignaciones_fecha ON limpieza.asignaciones_limpieza (fecha_programada);
CREATE INDEX IF NOT EXISTS idx_asignaciones_empleado ON limpieza.asignaciones_limpieza (empleado_id);

-- ----------------------------------------------------------------------------
-- limpieza.registros_limpieza: vincular a la asignación que la generó.
-- ----------------------------------------------------------------------------
ALTER TABLE limpieza.registros_limpieza ADD COLUMN IF NOT EXISTS asignacion_id INT;

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'registros_limpieza_asignacion_fk'
          AND conrelid = 'limpieza.registros_limpieza'::regclass
    ) THEN
        ALTER TABLE limpieza.registros_limpieza
            ADD CONSTRAINT registros_limpieza_asignacion_fk
            FOREIGN KEY (asignacion_id) REFERENCES limpieza.asignaciones_limpieza(id);
    END IF;
END $$;

CREATE INDEX IF NOT EXISTS idx_registros_asignacion ON limpieza.registros_limpieza (asignacion_id);