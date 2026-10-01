<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// ==========================================================================
// AUTENTICACION
// ==========================================================================
// GET  /login   formulario (redirige a / si ya hay sesión)
// POST /login   valida credenciales (csrf)
// GET|POST /logout  cierra la sesión. El POST lo usa el formulario de
//                    app/Views/errors/prohibido.php para salir de un 403.
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::autenticar', ['filter' => 'csrf']);
$routes->get('logout', 'Auth::salir');
$routes->post('logout', 'Auth::salir', ['filter' => 'csrf']);

// ==========================================================================
// ANIMALES Y ESPECIES
// ==========================================================================
// El grupo ya exige sesión (auth) y CSRF en los POST (csrf).
//   lectura del listado   → administrador, supervisor
//   escritura (crear,
//   editar, baja)        → administrador
$routes->group('animales', ['filter' => ['auth', 'csrf']], static function ($routes) {
    $routes->get('/', 'Animales\Animales::index', ['filter' => 'rol:administrador,supervisor']);

    // Escritura: solo administrador
    $routes->group('', ['filter' => 'rol:administrador'], static function ($routes) {
        $routes->get('nuevo', 'Animales\Animales::nuevo');
        $routes->post('guardar', 'Animales\Animales::guardar');
        $routes->get('editar/(:num)', 'Animales\Animales::editar/$1');
        $routes->post('actualizar/(:num)', 'Animales\Animales::actualizar/$1');
        $routes->post('baja/(:num)', 'Animales\Animales::darDeBaja/$1');

        // Especies: TODO el CRUD es de escritura, así que todo va aquí.
        $routes->get('especies', 'Animales\Especies::index');
        $routes->get('especies/nueva', 'Animales\Especies::nueva');
        $routes->post('especies/guardar', 'Animales\Especies::guardar');
        $routes->get('especies/editar/(:num)', 'Animales\Especies::editar/$1');
        $routes->post('especies/actualizar/(:num)', 'Animales\Especies::actualizar/$1');
        $routes->post('especies/eliminar/(:num)', 'Animales\Especies::eliminar/$1');
    });
});

// ===== FIN ANIMALES =====

// ===== CONTROL CLINICO =====
// TODO(roles): aquí solo se exige sesión (auth), sin filtro de rol, porque en
// core.roles NO existe un rol veterinario ni cuidador y no se quiere bloquear a
// nadie. Cuando se defina un rol clínico, añadir 'rol:<rol>' al grupo.
$routes->group('clinico', ['filter' => ['auth', 'csrf']], static function ($routes) {
    $routes->get('/', 'Clinico::index');
    $routes->post('guardar', 'Clinico::guardar');
    $routes->get('editar/(:num)', 'Clinico::editar/$1');
    $routes->post('actualizar/(:num)', 'Clinico::actualizar/$1');
    $routes->get('eliminar/(:num)', 'Clinico::eliminar/$1');

    // Vacunas
    $routes->get('vacunas', 'ClinicoVacunas::index');
    $routes->post('vacunas/guardar', 'ClinicoVacunas::guardar');
    $routes->post('vacunas/guardar-vacuna', 'ClinicoVacunas::guardarVacuna');
    $routes->get('vacunas/eliminar/(:num)', 'ClinicoVacunas::eliminar/$1');
});
// ===== FIN CONTROL CLINICO =====

// ===== LIMPIEZA =====
$routes->group('limpieza', ['filter' => ['auth', 'csrf']], static function ($routes) {
    $routes->get('/', 'Limpieza\Inicio::index');

    // Zonas (solo administrador)
    $routes->group('zonas', ['filter' => 'rol:administrador'], static function ($routes) {
        $routes->get('/', 'Limpieza\Zonas::index');
        $routes->get('nueva', 'Limpieza\Zonas::nueva');
        $routes->get('editar/(:num)', 'Limpieza\Zonas::editar/$1');
        $routes->post('guardar', 'Limpieza\Zonas::guardar');
        $routes->post('actualizar/(:num)', 'Limpieza\Zonas::actualizar/$1');
        $routes->post('desactivar/(:num)', 'Limpieza\Zonas::desactivar/$1');
    });

    // Tareas (administrador y supervisor)
    $routes->group('tareas', ['filter' => 'rol:administrador,supervisor'], static function ($routes) {
        $routes->get('/', 'Limpieza\Tareas::index');
        $routes->get('nueva', 'Limpieza\Tareas::nueva');
        $routes->get('editar/(:num)', 'Limpieza\Tareas::editar/$1');
        $routes->get('insumos/(:num)', 'Limpieza\Tareas::insumos/$1');
        $routes->post('guardar', 'Limpieza\Tareas::guardar');
        $routes->post('actualizar/(:num)', 'Limpieza\Tareas::actualizar/$1');
        $routes->post('desactivar/(:num)', 'Limpieza\Tareas::desactivar/$1');
        $routes->post('insumos/(:num)/agregar', 'Limpieza\Tareas::agregarInsumo/$1');
        $routes->post('insumos/quitar/(:num)', 'Limpieza\Tareas::quitarInsumo/$1');
    });

    // Asignaciones (solo supervisor)
    $routes->group('asignaciones', ['filter' => 'rol:supervisor'], static function ($routes) {
        $routes->get('nueva', 'Limpieza\Asignaciones::nueva');
        $routes->get('reasignar/(:num)', 'Limpieza\Asignaciones::reasignar/$1');
        $routes->post('guardar', 'Limpieza\Asignaciones::guardar');
        $routes->post('reasignar/(:num)', 'Limpieza\Asignaciones::guardarReasignacion/$1');
    });

    // Seguimiento (supervisor y administrador en lectura)
    $routes->group('seguimiento', ['filter' => 'rol:supervisor,administrador'], static function ($routes) {
        $routes->get('/', 'Limpieza\Seguimiento::index');
        $routes->get('detalle/(:num)', 'Limpieza\Seguimiento::detalle/$1');
    });

    // Mis asignaciones (empleado de limpieza)
    $routes->group('mis-tareas', ['filter' => 'rol:empleado_limpieza'], static function ($routes) {
        $routes->get('/', 'Limpieza\MisTareas::index');
        $routes->get('detalle/(:num)', 'Limpieza\MisTareas::detalle/$1');
        $routes->post('iniciar/(:num)', 'Limpieza\MisTareas::iniciar/$1');
        $routes->post('finalizar/(:num)', 'Limpieza\MisTareas::finalizar/$1');
        $routes->post('observaciones/(:num)', 'Limpieza\MisTareas::observaciones/$1');
    });

    // Reportes (administrador y supervisor)
    $routes->group('reportes', ['filter' => 'rol:administrador,supervisor'], static function ($routes) {
        $routes->get('/', 'Limpieza\Reportes::index');
        $routes->get('imprimir', 'Limpieza\Reportes::imprimir');
        $routes->get('exportar', 'Limpieza\Reportes::exportarCsv');
    });
});
// ===== ALIMENTACIÓN =====
// TODO(roles): solo se exige sesión (auth), sin filtro de rol: no existe un rol
// de cuidador de alimentación en core.roles. Cuando exista, añadir 'rol:<rol>'.
$routes->group('alimentacion', ['filter' => ['auth', 'csrf']], static function ($routes) {
    $routes->get('/', 'Alimentacion\Inicio::index');

    // Dietas por animal
    $routes->get('dietas', 'Alimentacion\Dietas::index');
    $routes->post('dietas/guardar', 'Alimentacion\Dietas::guardar');
    $routes->get('dietas/eliminar/(:num)', 'Alimentacion\Dietas::eliminar/$1');

    // Horarios de alimentación
    $routes->get('horarios', 'Alimentacion\Horarios::index');
    $routes->post('horarios/guardar', 'Alimentacion\Horarios::guardar');
    $routes->get('horarios/eliminar/(:num)', 'Alimentacion\Horarios::eliminar/$1');

    // Registros de alimentación
    $routes->get('registros', 'Alimentacion\Registros::index');
    $routes->post('registros/guardar', 'Alimentacion\Registros::guardar');
    $routes->get('registros/eliminar/(:num)', 'Alimentacion\Registros::eliminar/$1');

    // Inventario de alimentos
    $routes->get('inventario', 'Alimentacion\Inventario::index');
    $routes->post('inventario/guardar', 'Alimentacion\Inventario::guardar');
    $routes->get('inventario/eliminar/(:num)', 'Alimentacion\Inventario::eliminar/$1');

    // Reportes de alimentación
    $routes->get('reportes', 'Alimentacion\Reportes::index');
});
// ===== FIN ALIMENTACIÓN =====


// ==========================================================================
// PORTAL PÚBLICO (compra de entradas)
// ==========================================================================
$routes->group('portal', ['filter' => 'csrf'], function ($routes) {
    $routes->get('/', 'Portal::index');
    $routes->get('comprar', 'Portal::comprar');
    $routes->post('confirmar', 'Portal::confirmar');
    $routes->get('ticket/(:num)', 'Portal::ticket/$1');
});

// ==========================================================================
// ENTRADAS Y PROMOCIONES (backoffice)
// ==========================================================================
$routes->group('entradas', ['filter' => ['auth', 'csrf']], function ($routes) {
    $routes->get('/', 'Entradas\Inicio::index');

    // Punto de venta (taquilla)
    $routes->group('taquilla', ['filter' => 'rol:administrador,cajero,supervisor'], function ($routes) {
        $routes->get('/', 'Entradas\PuntoVenta::index');
        $routes->get('nueva', 'Entradas\PuntoVenta::nueva');
        $routes->post('guardar', 'Entradas\PuntoVenta::guardar');
        $routes->get('detalle/(:num)', 'Entradas\PuntoVenta::detalle/$1');
        $routes->post('anular/(:num)', 'Entradas\PuntoVenta::anular/$1');
    });

    // Tarifas
    $routes->group('tarifas', ['filter' => 'rol:admin_mercadeo,administrador'], function ($routes) {
        $routes->get('/', 'Entradas\Tarifas::index');
        $routes->get('nueva', 'Entradas\Tarifas::nueva');
        $routes->post('guardar', 'Entradas\Tarifas::guardar');
        $routes->get('editar/(:num)', 'Entradas\Tarifas::editar/$1');
        $routes->post('actualizar/(:num)', 'Entradas\Tarifas::actualizar/$1');
        $routes->post('desactivar/(:num)', 'Entradas\Tarifas::desactivar/$1');
    });

    // Reportes
    $routes->group('reportes', ['filter' => 'rol:administrador,supervisor'], function ($routes) {
        $routes->get('/', 'Entradas\Reportes::index');
        $routes->get('imprimir', 'Entradas\Reportes::imprimir');
        $routes->get('exportar', 'Entradas\Reportes::exportarCsv');
    });

    // Promociones
    $routes->group('promociones', ['filter' => 'rol:admin_mercadeo,administrador'], function ($routes) {
        $routes->get('/', 'Entradas\Promociones::index');
        $routes->get('nueva', 'Entradas\Promociones::nueva');
        $routes->post('guardar', 'Entradas\Promociones::guardar');
        $routes->get('editar/(:num)', 'Entradas\Promociones::editar/$1');
        $routes->post('actualizar/(:num)', 'Entradas\Promociones::actualizar/$1');
        $routes->post('desactivar/(:num)', 'Entradas\Promociones::desactivar/$1');
    });
});

// ==========================================================================
// HERRAMIENTAS DE DESARROLLO (solo CI_ENVIRONMENT=development)
// ==========================================================================
if (ENVIRONMENT === 'development') {
    $routes->get('dev/sesion/(:segment)', 'Dev::sesion/$1');
    $routes->get('dev/cerrar', 'Dev::cerrar');
}