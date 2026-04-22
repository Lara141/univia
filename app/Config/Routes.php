<?php
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --- LOGIN Y SESIÓN ---
$routes->get('/', 'Home::index'); 
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/cerrar_sesion', 'Auth::logout');

// --- PANEL Y PUBLICACIONES ---
$routes->get('publicaciones/propias', 'Home::publicaciones');
$routes->get('publicaciones/nueva', 'Home::nueva_publicacion');
$routes->post('publicaciones/guardar', 'Home::guardar_publicacion');

// --- REGISTRO ---
$routes->get('inicio/registro', 'Home::registro_vista');
$routes->post('inicio/procesar_registro', 'Home::procesar_registro');

// Rutas para editar y eliminar
$routes->get('publicaciones/editar/(:num)', 'Home::editar_publicacion/$1');
$routes->get('publicaciones/eliminar/(:num)', 'Home::eliminar_publicacion/$1');