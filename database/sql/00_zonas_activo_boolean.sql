-- ============================================================================
-- core.zonas.activo: SMALLINT -> BOOLEAN  (cirugia minima, sin tocar el resto)
-- ----------------------------------------------------------------------------
-- Por que: en la BD compartida la columna ya existe como SMALLINT (la aplico
-- una version anterior de la migracion). Postgres no tiene conversion
-- implicita, asi que `where('activo', true)` falla con
--     ERROR:  operator does not exist: smallint = boolean
-- y eso tumbaba /limpieza/zonas, /limpieza/tareas y /limpieza/seguimiento.
--
-- Es idempotente: si la columna ya es BOOLEAN, el bloque DO no hace nada.
-- Si la columna no existiera, el ALTER TABLE final la crea ya como BOOLEAN.
--
-- ⚠️ Requiere aprobacion del equipo (Supabase compartida).
-- Es equivalente al bloque correspondiente de 01_limpieza_cambios.sql.
-- ============================================================================

-- 1) Ver que hay antes de tocar nada (esta linea es inocua, se puede borrar).
SELECT table_schema, table_name, column_name, data_type, is_nullable, column_default
FROM information_schema.columns
WHERE table_schema = 'core' AND table_name = 'zonas' AND column_name = 'activo';

-- 2) Convertir SOLO si hoy es SMALLINT.
DO $$
BEGIN
    IF EXISTS (
        SELECT 1
        FROM information_schema.columns
        WHERE table_schema = 'core'
          AND table_name   = 'zonas'
          AND column_name  = 'activo'
          AND data_type    = 'smallint'
    ) THEN
        ALTER TABLE core.zonas ALTER COLUMN activo DROP DEFAULT;
        ALTER TABLE core.zonas ALTER COLUMN activo TYPE BOOLEAN USING activo <> 0;
        ALTER TABLE core.zonas ALTER COLUMN activo SET DEFAULT TRUE;
    END IF;
END $$;

-- 3) Red de seguridad: si no existia, crearla como BOOLEAN.
ALTER TABLE core.zonas ADD COLUMN IF NOT EXISTS activo BOOLEAN NOT NULL DEFAULT TRUE;

-- 4) Verificar (solo SELECT). Debe salir data_type = 'boolean'.
SELECT table_schema, table_name, column_name, data_type, is_nullable, column_default
FROM information_schema.columns
WHERE table_schema = 'core' AND table_name = 'zonas' AND column_name = 'activo';
