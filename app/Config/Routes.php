<?php
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Cuando entres a localhost/dashboard/ verás tus publicaciones
$routes->get('/', 'Home::index'); 

// Esta es la ruta exacta que tu botón "Nueva Publicación" está buscando
$routes->get('publicaciones/nueva', 'Home::nueva_publicacion');

$routes->get('inicio/registro', 'Home::registro');