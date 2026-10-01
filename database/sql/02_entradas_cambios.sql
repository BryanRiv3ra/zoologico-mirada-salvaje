-- ============================================================================
-- MÓDULO DE ENTRADAS Y PROMOCIONES — cambios sobre el DDL original
-- Ya aplicado en Supabase. Solo referencia.
--
-- El código usa el E-R real: entradas.entradas (un boleto por fila) y
-- entradas.pagos. NO usa entradas.promocion_tarifa, entradas.ventas,
-- entradas.boletos ni entradas.pagos.venta_id, así que este script se reduce a
-- las columnas que sí usa el código: activo en tarifas y promociones.
--
-- Idempotente: seguro de ejecutar varias veces.
-- ⚠️ No ejecutar contra la Supabase compartida sin aprobación del equipo.
-- ============================================================================

-- ----------------------------------------------------------------------------
-- entradas.tarifas: agregar activo (se pueden desactivar sin borrar histórico).
-- ----------------------------------------------------------------------------
ALTER TABLE entradas.tarifas ADD COLUMN IF NOT EXISTS activo BOOLEAN NOT NULL DEFAULT TRUE;

-- ----------------------------------------------------------------------------
-- entradas.promociones: agregar activo y código único.
-- En Supabase el UNIQUE ya existe (nombre autogenerado: promociones_codigo_key).
-- El bloque de abajo NO lo duplica: solo lo crea si no hubiera ningún UNIQUE
-- sobre `codigo`.
-- ----------------------------------------------------------------------------
ALTER TABLE entradas.promociones ADD COLUMN IF NOT EXISTS activo BOOLEAN NOT NULL DEFAULT TRUE;

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint
        WHERE conrelid = 'entradas.promociones'::regclass
          AND contype = 'u'
          AND pg_get_constraintdef(oid) = 'UNIQUE (codigo)'
    ) THEN
        ALTER TABLE entradas.promociones
            ADD CONSTRAINT promociones_codigo_uniq UNIQUE (codigo);
    END IF;
END $$;
