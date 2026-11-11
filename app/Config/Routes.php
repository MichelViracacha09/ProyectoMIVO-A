<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

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
$routes->get('register', 'Auth::register');
$routes->post('register/process', 'Auth::processRegister');
$routes->get('motos', 'Motos::index');
$routes->get('motos/new', 'Motos::create');
$routes->post('motos/store', 'Motos::store');
$routes->get('motos/edit/(:num)', 'Motos::edit/$1');
$routes->post('motos/update/(:num)', 'Motos::update/$1');
$routes->get('motos/delete/(:num)', 'Motos::delete/$1');
$routes->get('citas', 'Citas::index');
$routes->get('citas/new', 'Citas::create');
$routes->post('citas/store', 'Citas::store');
$routes->get('citas/edit/(:num)', 'Citas::edit/$1');
$routes->post('citas/update/(:num)', 'Citas::update/$1');
$routes->get('citas/delete/(:num)', 'Citas::delete/$1');
$routes->get('servicios', 'Servicios::index');
$routes->get('servicios/new', 'Servicios::create');
$routes->post('servicios/store', 'Servicios::store');
$routes->get('servicios/edit/(:num)', 'Servicios::edit/$1');
$routes->post('servicios/update/(:num)', 'Servicios::update/$1');
$routes->get('servicios/delete/(:num)', 'Servicios::delete/$1');