<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

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

    // Promociones
    $routes->group('promociones', ['filter' => 'rol:admin_mercadeo,administrador'], function ($routes) {
        $routes->get('/', 'Entradas\Promociones::index');
        $routes->get('nueva', 'Entradas\Promociones::nueva');
        $routes->post('guardar', 'Entradas\Promociones::guardar');
        $routes->get('editar/(:num)', 'Entradas\Promociones::editar/$1');
        $routes->post('actualizar/(:num)', 'Entradas\Promociones::actualizar/$1');
        $routes->post('desactivar/(:num)', 'Entradas\Promociones::desactivar/$1');
    });

    // Control de acceso (validación de boletos en ingreso)
    $routes->group('acceso', ['filter' => 'rol:control_acceso,administrador'], function ($routes) {
        $routes->get('/', 'Entradas\Acceso::index');
        $routes->get('buscar', 'Entradas\Acceso::buscar');
        $routes->post('validar/(:num)', 'Entradas\Acceso::validar/$1');
    });

    // Reportes
    $routes->group('reportes', ['filter' => 'rol:administrador,supervisor'], function ($routes) {
        $routes->get('/', 'Entradas\Reportes::index');
        $routes->get('imprimir', 'Entradas\Reportes::imprimir');
        $routes->get('exportar', 'Entradas\Reportes::exportarCsv');
    });
});

// ==========================================================================
// HERRAMIENTAS DE DESARROLLO (solo CI_ENVIRONMENT=development)
// ==========================================================================
if (ENVIRONMENT === 'development') {
    $routes->get('dev/sesion/(:segment)', 'Dev::sesion/$1');
    $routes->get('dev/cerrar', 'Dev::cerrar');
}