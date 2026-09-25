<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$apiResources = [
	'especies',
	'zonas',
	'animales',
	'empleados',
	'usuarios',
	'roles',
	'usuario-rol',
	'proveedores',
	'inventario',
	'movimientos-inventario',
	'dietas',
	'dieta-detalle',
	'horarios-alimentacion',
	'registros-alimentacion',
	'tareas-limpieza',
	'registros-limpieza',
	'vacunas',
	'historial-clinico',
	'tratamientos',
	'aplicaciones-vacunas',
	'eventos',
	'promociones',
	'tarifas',
	'visitantes',
	'entradas',
	'pagos',
];

foreach ($apiResources as $resource) {
	$routes->get('api/' . $resource, 'ApiCrud::index/' . $resource);
	$routes->post('api/' . $resource, 'ApiCrud::create/' . $resource);
	$routes->get('api/' . $resource . '/(:num)', 'ApiCrud::show/' . $resource . '/$1');
	$routes->put('api/' . $resource . '/(:num)', 'ApiCrud::update/' . $resource . '/$1');
	$routes->patch('api/' . $resource . '/(:num)', 'ApiCrud::update/' . $resource . '/$1');
	$routes->delete('api/' . $resource . '/(:num)', 'ApiCrud::delete/' . $resource . '/$1');
}
