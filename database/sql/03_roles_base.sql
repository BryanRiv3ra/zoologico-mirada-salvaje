-- ============================================================================
-- ROLES BASE DEL SISTEMA
-- ----------------------------------------------------------------------------
-- Inserta los 6 roles que usan los filtros de autorización (app/Filters/RolFilter.php)
-- en core.roles.nombre:
--
--   administrador    acceso total
--   supervisor       limpieza (tareas, asignaciones, seguimiento, reportes)
--   empleado_limpieza  limpieza/mis-tareas
--   admin_mercadeo   entradas (tarifas, promociones)
--   cajero           entradas/taquilla
--   control_acceso   reserved
--
-- Idempotente: la tabla tiene UNIQUE (nombre) (constraint roles_nombre_key),
-- asi que se puede repetir cuantas veces haga falta sin duplicar nada.
--
-- ⚠️ Supabase es compartida: este script lo ejecuta una persona del equipo con
--    aprobación, no el desarrollo. No borra ni modifica filas existentes.
-- ============================================================================

BEGIN;

INSERT INTO core.roles (nombre, descripcion) VALUES
    ('administrador',     'Acceso total a todos los módulos del sistema.'),
    ('supervisor',        'Supervisa limpieza: tareas, asignaciones, seguimiento y reportes.'),
    ('empleado_limpieza', 'Ejecuta sus tareas de limpieza asignadas y registra observaciones.'),
    ('admin_mercadeo',    'Administra tarifas y promociones de entradas.'),
    ('cajero',            'Opera el punto de venta (taquilla) y anula ventas.'),
    ('control_acceso',    'Control de acceso de visitantes al zoológico.')
ON CONFLICT (nombre) DO NOTHING;

COMMIT;

-- ----------------------------------------------------------------------------
-- Verificación (solo SELECT). Debe devolver 6 filas.
-- ----------------------------------------------------------------------------
SELECT nombre, descripcion
FROM core.roles
WHERE nombre IN (
    'administrador', 'supervisor', 'empleado_limpieza',
    'admin_mercadeo', 'cajero', 'control_acceso'
)
ORDER BY nombre;
