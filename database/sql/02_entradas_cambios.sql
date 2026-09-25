-- ============================================================================
-- MÓDULO DE ENTRADAS Y PROMOCIONES — cambios sobre el DDL original
-- Idempotente: seguro de ejecutar varias veces (IF NOT EXISTS / additions).
-- ⚠️ No ejecutar contra Supabase compartida sin aprobación del equipo.
-- ============================================================================

-- Esquema de trabajo (ya existe en el DDL original).
CREATE SCHEMA IF NOT EXISTS entradas;

-- ----------------------------------------------------------------------------
-- entradas.tarifas: agregar activo (se pueden desactivar sin borrar histórico).
-- ----------------------------------------------------------------------------
ALTER TABLE entradas.tarifas ADD COLUMN IF NOT EXISTS activo BOOLEAN NOT NULL DEFAULT TRUE;

-- ----------------------------------------------------------------------------
-- entradas.promociones: agregar activo y código único (D3).
-- ----------------------------------------------------------------------------
ALTER TABLE entradas.promociones ADD COLUMN IF NOT EXISTS activo BOOLEAN NOT NULL DEFAULT TRUE;

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'promociones_codigo_uniq'
          AND conrelid = 'entradas.promociones'::regclass
    ) THEN
        ALTER TABLE entradas.promociones
            ADD CONSTRAINT promociones_codigo_uniq UNIQUE (codigo);
    END IF;
END $$;

-- ----------------------------------------------------------------------------
-- entradas.promocion_tarifa (nueva): a qué tarifas aplica cada promoción.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS entradas.promocion_tarifa (
    id           SERIAL PRIMARY KEY,
    promocion_id INT NOT NULL REFERENCES entradas.promociones(id),
    tarifa_id    INT NOT NULL REFERENCES entradas.tarifas(id)
);

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'promocion_tarifa_uniq'
          AND conrelid = 'entradas.promocion_tarifa'::regclass
    ) THEN
        ALTER TABLE entradas.promocion_tarifa
            ADD CONSTRAINT promocion_tarifa_uniq UNIQUE (promocion_id, tarifa_id);
    END IF;
END $$;

-- ----------------------------------------------------------------------------
-- entradas.ventas (nueva): cabecera de venta (taquilla o portal).
-- D2: motivo_anulacion · D3: codigo UNIQUE · D6: cliente/empleado opcionales.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS entradas.ventas (
    id               SERIAL PRIMARY KEY,
    codigo           VARCHAR(12) NOT NULL,
    fecha            TIMESTAMP NOT NULL DEFAULT now(),
    empleado_id      INT REFERENCES core.empleados(id),
    cliente_id       INT REFERENCES entradas.visitantes(id),
    punto_venta      VARCHAR(50),
    tipo_pago        VARCHAR(20) NOT NULL,
    tipo_venta       VARCHAR(20) NOT NULL,
    subtotal         NUMERIC(10,2) NOT NULL DEFAULT 0,
    descuento        NUMERIC(10,2) NOT NULL DEFAULT 0,
    total            NUMERIC(10,2) NOT NULL,
    estado           VARCHAR(15) NOT NULL DEFAULT 'completada',
    motivo_anulacion TEXT,
    fecha_anulacion  TIMESTAMP,
    anulado_por      INT REFERENCES core.empleados(id)
);

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'ventas_codigo_uniq'
          AND conrelid = 'entradas.ventas'::regclass
    ) THEN
        ALTER TABLE entradas.ventas ADD CONSTRAINT ventas_codigo_uniq UNIQUE (codigo);
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'ventas_tipo_pago_chk'
          AND conrelid = 'entradas.ventas'::regclass
    ) THEN
        ALTER TABLE entradas.ventas
            ADD CONSTRAINT ventas_tipo_pago_chk
            CHECK (tipo_pago IN ('efectivo','tarjeta','transferencia'));
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'ventas_tipo_venta_chk'
          AND conrelid = 'entradas.ventas'::regclass
    ) THEN
        ALTER TABLE entradas.ventas
            ADD CONSTRAINT ventas_tipo_venta_chk
            CHECK (tipo_venta IN ('taquilla','portal'));
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'ventas_estado_chk'
          AND conrelid = 'entradas.ventas'::regclass
    ) THEN
        ALTER TABLE entradas.ventas
            ADD CONSTRAINT ventas_estado_chk
            CHECK (estado IN ('completada','anulada'));
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'ventas_total_gt0'
          AND conrelid = 'entradas.ventas'::regclass
    ) THEN
        ALTER TABLE entradas.ventas ADD CONSTRAINT ventas_total_gt0 CHECK (total > 0);
    END IF;
END $$;

CREATE INDEX IF NOT EXISTS idx_ventas_fecha ON entradas.ventas (fecha);
CREATE INDEX IF NOT EXISTS idx_ventas_empleado ON entradas.ventas (empleado_id);

-- ----------------------------------------------------------------------------
-- entradas.boletos (nueva): boleto individual con su código QR.
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS entradas.boletos (
    id           SERIAL PRIMARY KEY,
    venta_id     INT NOT NULL REFERENCES entradas.ventas(id),
    tarifa_id    INT NOT NULL REFERENCES entradas.tarifas(id),
    promocion_id INT REFERENCES entradas.promociones(id),
    fecha_visita DATE NOT NULL,
    codigo_qr    VARCHAR(100) NOT NULL,
    precio       NUMERIC(10,2) NOT NULL DEFAULT 0,
    estado       VARCHAR(12) NOT NULL DEFAULT 'emitido'
);

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'boletos_codigo_qr_uniq'
          AND conrelid = 'entradas.boletos'::regclass
    ) THEN
        ALTER TABLE entradas.boletos ADD CONSTRAINT boletos_codigo_qr_uniq UNIQUE (codigo_qr);
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'boletos_estado_chk'
          AND conrelid = 'entradas.boletos'::regclass
    ) THEN
        ALTER TABLE entradas.boletos
            ADD CONSTRAINT boletos_estado_chk
            CHECK (estado IN ('emitido','usado','anulado'));
    END IF;
END $$;

CREATE INDEX IF NOT EXISTS idx_boletos_venta ON entradas.boletos (venta_id);
CREATE INDEX IF NOT EXISTS idx_boletos_qr ON entradas.boletos (codigo_qr);

-- ----------------------------------------------------------------------------
-- entradas.pagos: vincular el pago a la venta (columna nueva; la anterior
-- entrada_id se conserva por compatibilidad con el DDL original).
-- ----------------------------------------------------------------------------
ALTER TABLE entradas.pagos ADD COLUMN IF NOT EXISTS venta_id INT;

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'pagos_venta_fk'
          AND conrelid = 'entradas.pagos'::regclass
    ) THEN
        ALTER TABLE entradas.pagos
            ADD CONSTRAINT pagos_venta_fk
            FOREIGN KEY (venta_id) REFERENCES entradas.ventas(id);
    END IF;
END $$;

CREATE INDEX IF NOT EXISTS idx_pagos_venta ON entradas.pagos (venta_id);