# Módulo de Limpieza

Subsistema operativo: programación y seguimiento de tareas de limpieza del zoológico.

## Alcance

- **Zonas** (`core.zonas`): catálogo activo/inactivo. Solo administrador.
- **Tareas de limpieza** (`limpieza.tareas_limpieza`) con **insumos asociados** (`limpieza.tarea_insumo` → `core.inventario`). Administrador y supervisor.
- **Asignaciones** (`limpieza.asignaciones_limpieza`): programar una tarea a un empleado de limpieza para una fecha. Solo supervisor.
  - Restricción: una tarea no puede asignarse dos veces a la misma fecha (UNIQUE `tarea_id, fecha_programada`).
  - Estado: `pendiente` → `en_curso` → `listo` (transiciones validadas en `ServicioAsignaciones`).
  - Reasignación (cambiar empleado de una asignación no iniciada).
- **Mi Tareas**: el empleado de limpieza ve sus asignaciones, las inicia, las finaliza y registra observaciones.
- **Seguimiento** (tablero kanban) y **Reportes** (rango de fechas, por zona/tipo/empleado; exportación CSV e impresión) para administrador y supervisor.

## Roles y rutas

| Ruta | Rol |
|---|---|
| `/limpieza` (inicio) | cualquiera autenticado |
| `/limpieza/zonas*` | administrador |
| `/limpieza/tareas*` | administrador, supervisor |
| `/limpieza/asignaciones*` | supervisor |
| `/limpieza/seguimiento*` | supervisor, administrador |
| `/limpieza/mis-tareas*` | empleado_limpieza |
| `/limpieza/reportes*` | administrador, supervisor |

Los nombres de rol viven en `core.roles` (`administrador`, `supervisor`, `empleado_limpieza`, etc.).
La sesión debe exponer: `usuario_id`, `empleado_id`, `nombre`, `email`, `roles[]`.

## Requisitos de base de datos

Sobre el DDL original, aplicar `database/sql/01_limpieza_cambios.sql` (idempotente):

- `core.zonas.activo` (booleano, por defecto `true`).
- `limpieza.tareas_limpieza.activo` + UNIQUE `(zona_id, descripcion)`.
- Tablas nuevas: `limpieza.tarea_insumo` y `limpieza.asignaciones_limpieza`.
- `limpieza.registros_limpieza.asignacion_id` (FK a la asignación que la generó).

## Integración con autenticación (core)

Las rutas usan los filtros placeholder `auth`/`rol` (`app/Filters`). El equipo de
autenticación debe reemplazarlos por la lógica real (leer sesión y `core.roles`) sin
cambiar la API (nombre de alias y parámetro `rol:`). Herramienta de desarrollo:
`/dev/sesion/{rol}` y `/dev/cerrar` (`app/Controllers/Dev.php`) — **solo disponibles en
`CI_ENVIRONMENT=development` y debe eliminarse al integrar core-autenticación.**

## Notas técnicas

- Los formularios POST incluyen `csrf_field()` (el grupo aplica el filtro `csrf`).
- Las operaciones de asignación corren en transacción y devuelven `AsignacionException`
  ante violaciones de negocio (duplicado, transición inválida, empleado sin rol).
- Reportes e impresión calculan cumplimiento y duración media sin tocar el esquema del resto.
- PHP 8.2+ (usa `enum`), PostgreSQL mediante controlador `Postgre`.