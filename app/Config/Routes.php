<?php
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ═══════════════════════════════════════════════════════════════
// AUTENTICACIÓN (AuthController)
// ═══════════════════════════════════════════════════════════════
$routes->get('/', 'AuthController::index');
$routes->post('auth/login', 'AuthController::login');
$routes->get('auth/logout', 'AuthController::logout');

// Registro
$routes->get('auth/registro', 'AuthController::registro_vista');
$routes->post('auth/procesar_registro', 'AuthController::procesar_registro');

// ═══════════════════════════════════════════════════════════════
// PUBLICACIONES (PublicacionController)
// ═══════════════════════════════════════════════════════════════
$routes->get('publicaciones/propias', 'PublicacionController::propias');
$routes->get('publicaciones/crear', 'PublicacionController::mostrarFormulario');
$routes->post('publicaciones/guardar', 'PublicacionController::guardar');
$routes->get('publicaciones/editar/(:num)', 'PublicacionController::editar/$1');
$routes->post('publicaciones/actualizar/(:num)', 'PublicacionController::actualizar/$1');
$routes->get('publicaciones/eliminar/(:num)', 'PublicacionController::eliminar/$1');

// ═══════════════════════════════════════════════════════════════
// APIs REST (ApiController)
// ═══════════════════════════════════════════════════════════════

// Catálogos (materias, tipos, acuerdos)
$routes->get('api/materias', 'ApiController::materias');
$routes->get('api/tipos', 'ApiController::tipos');
$routes->get('api/acuerdos', 'ApiController::acuerdos');

// Publicaciones
$routes->get('api/publicaciones', 'ApiController::publicacionesUsuario');
$routes->get('api/publicaciones/(:num)', 'ApiController::publicacion/$1');
$routes->get('api/publicaciones/materia/(:num)', 'ApiController::porMateria/$1');

// ═══════════════════════════════════════════════════════════════
// COMPATIBILIDAD HACIA ATRÁS (rutas antiguas)
// ═══════════════════════════════════════════════════════════════
$routes->get('auth/cerrar_sesion', 'AuthController::logout');
$routes->get('publicaciones/nueva', 'PublicacionController::mostrarFormulario');
$routes->get('inicio/registro', 'AuthController::registro_vista');
$routes->post('inicio/procesar_registro', 'AuthController::procesar_registro');


$routes->get('materiales/buscar', 'BuscadorController::buscar');

$routes->get('materiales/explorar', 'BuscadorController::explorar');
$routes->get('publicaciones/explorar', 'BuscadorController::explorar');


// Ruta para abrir/descargar el PDF en otra pestaña
$routes->get('publicaciones/descargar/(:num)', 'DescargarController::descargar/$1');

// Ruta para procesar el formulario de pago simulado 
$routes->post('publicaciones/pagar/(:num)', 'PagoController::procesarPago/$1');