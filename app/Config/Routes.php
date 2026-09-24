<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('clinico', 'Clinico::index');
$routes->post('clinico/guardar', 'Clinico::guardar');
$routes->get('clinico/editar/(:num)', 'Clinico::editar/$1');
$routes->post('clinico/actualizar/(:num)', 'Clinico::actualizar/$1');
$routes->get('clinico/eliminar/(:num)', 'Clinico::eliminar/$1');