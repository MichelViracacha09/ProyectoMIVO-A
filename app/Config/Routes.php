<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Autenticación
$routes->get('/', 'Auth::index');
$routes->get('login', 'Auth::index');
$routes->post('login/process', 'Auth::processLogin');
$routes->get('logout', 'Auth::logout');
$routes->get('home', 'Home::index');
$routes->get('register', 'Auth::register');
$routes->post('register/process', 'Auth::processRegister');

$routes->get('tasks', 'Tasks::index');
$routes->get('tasks/new', 'Tasks::create');
$routes->post('tasks/store', 'Tasks::store');
$routes->get('tasks/edit/(:num)', 'Tasks::edit/$1');
$routes->post('tasks/update/(:num)', 'Tasks::update/$1');
$routes->get('tasks/delete/(:num)', 'Tasks::delete/$1');

/*
 * Módulos con permisos por rol (tabla modulo_rol). El filtro 'permiso:<modulo>,<accion>'
 * valida en el servidor; la vista solo oculta botones por comodidad.
 */

// Clientes
$routes->get('clientes', 'Clientes::index', ['filter' => 'permiso:clientes,ver']);
$routes->get('clientes/new', 'Clientes::create', ['filter' => 'permiso:clientes,crear']);
$routes->post('clientes/store', 'Clientes::store', ['filter' => 'permiso:clientes,crear']);
$routes->get('clientes/show/(:num)', 'Clientes::show/$1', ['filter' => 'permiso:clientes,ver']);
$routes->get('clientes/edit/(:num)', 'Clientes::edit/$1', ['filter' => 'permiso:clientes,editar']);
$routes->post('clientes/update/(:num)', 'Clientes::update/$1', ['filter' => 'permiso:clientes,editar']);
$routes->post('clientes/estado/(:num)', 'Clientes::toggleEstado/$1', ['filter' => 'permiso:clientes,editar']);
$routes->post('clientes/delete/(:num)', 'Clientes::delete/$1', ['filter' => 'permiso:clientes,eliminar']);

// Motos
$routes->get('motos', 'Motos::index', ['filter' => 'permiso:motos,ver']);
$routes->get('motos/new', 'Motos::create', ['filter' => 'permiso:motos,crear']);
$routes->post('motos/store', 'Motos::store', ['filter' => 'permiso:motos,crear']);
$routes->get('motos/show/(:num)', 'Motos::show/$1', ['filter' => 'permiso:motos,ver']);
$routes->get('motos/edit/(:num)', 'Motos::edit/$1', ['filter' => 'permiso:motos,editar']);
$routes->post('motos/update/(:num)', 'Motos::update/$1', ['filter' => 'permiso:motos,editar']);
$routes->post('motos/delete/(:num)', 'Motos::delete/$1', ['filter' => 'permiso:motos,eliminar']);

// Citas (cancelar solo requiere ver: cada cliente cancela únicamente las suyas)
$routes->get('citas', 'Citas::index', ['filter' => 'permiso:citas,ver']);
$routes->get('citas/new', 'Citas::create', ['filter' => 'permiso:citas,crear']);
$routes->post('citas/store', 'Citas::store', ['filter' => 'permiso:citas,crear']);
$routes->get('citas/show/(:num)', 'Citas::show/$1', ['filter' => 'permiso:citas,ver']);
$routes->get('citas/edit/(:num)', 'Citas::edit/$1', ['filter' => 'permiso:citas,editar']);
$routes->post('citas/update/(:num)', 'Citas::update/$1', ['filter' => 'permiso:citas,editar']);
$routes->post('citas/cancel/(:num)', 'Citas::cancel/$1', ['filter' => 'permiso:citas,ver']);
$routes->post('citas/delete/(:num)', 'Citas::delete/$1', ['filter' => 'permiso:citas,eliminar']);

// Servicios
$routes->get('servicios', 'Servicios::index', ['filter' => 'permiso:servicios,ver']);
$routes->get('servicios/new', 'Servicios::create', ['filter' => 'permiso:servicios,crear']);
$routes->post('servicios/store', 'Servicios::store', ['filter' => 'permiso:servicios,crear']);
$routes->get('servicios/show/(:num)', 'Servicios::show/$1', ['filter' => 'permiso:servicios,ver']);
$routes->get('servicios/edit/(:num)', 'Servicios::edit/$1', ['filter' => 'permiso:servicios,editar']);
$routes->post('servicios/update/(:num)', 'Servicios::update/$1', ['filter' => 'permiso:servicios,editar']);
$routes->post('servicios/delete/(:num)', 'Servicios::delete/$1', ['filter' => 'permiso:servicios,eliminar']);

/*
 * Administración (solo rol Administrador): usuarios, roles y permisos.
 */
$routes->group('', ['filter' => 'role:admin'], static function ($routes) {
    $routes->get('usuarios', 'Usuarios::index');
    $routes->get('usuarios/new', 'Usuarios::create');
    $routes->post('usuarios/store', 'Usuarios::store');
    $routes->get('usuarios/show/(:num)', 'Usuarios::show/$1');
    $routes->get('usuarios/edit/(:num)', 'Usuarios::edit/$1');
    $routes->post('usuarios/update/(:num)', 'Usuarios::update/$1');

    $routes->get('roles', 'Roles::index');
    $routes->get('roles/new', 'Roles::create');
    $routes->post('roles/store', 'Roles::store');
    $routes->get('roles/show/(:num)', 'Roles::show/$1');
    $routes->get('roles/edit/(:num)', 'Roles::edit/$1');
    $routes->post('roles/update/(:num)', 'Roles::update/$1');
    $routes->post('roles/delete/(:num)', 'Roles::delete/$1');

    $routes->get('permisos', 'Permisos::index');
    $routes->post('permisos/save/(:num)', 'Permisos::save/$1');
});

// API REST de solo lectura (token Bearer). Login público, el resto con filtro apiAuth.
$routes->post('api/login', 'Api\Auth::login');
$routes->group('api', ['filter' => 'apiAuth', 'namespace' => 'App\Controllers\Api'], static function ($routes) {
    $routes->get('citas', 'Citas::index');
    $routes->get('citas/(:num)', 'Citas::show/$1');
    $routes->get('motos', 'Motos::index');
    $routes->get('motos/(:num)', 'Motos::show/$1');
    $routes->get('clientes', 'Clientes::index');
    $routes->get('clientes/(:num)', 'Clientes::show/$1');
});
