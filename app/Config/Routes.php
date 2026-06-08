<?php
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ═══════════════════════════════════════════════════════════════
// AUTENTICACIÓN (AuthController)
// ═══════════════════════════════════════════════════════════════
$routes->get('/', 'AuthController::index');
$routes->get('index/(:segment)', 'AuthController::index/$1');
$routes->post('auth/login', 'AuthController::login');
$routes->post('auth/login/(:segment)', 'AuthController::login/$1');
$routes->get('auth/logout', 'AuthController::logout');
$routes->get('auth/logout/(:segment)', 'AuthController::logout/$1');

// Registro
$routes->get('auth/registro', 'AuthController::registro_vista');
$routes->get('auth/registro/(:segment)', 'AuthController::registro_vista/$1');
$routes->post('auth/procesar_registro', 'AuthController::procesar_registro');
$routes->post('auth/procesar_registro/(:segment)', 'AuthController::procesar_registro/$1');

// ═══════════════════════════════════════════════════════════════
// PUBLICACIONES (PublicacionController)
// ═══════════════════════════════════════════════════════════════
$routes->group('publicaciones', static function ($routes) {
    // Este grupo podría usar un filtro de autenticación: ['filter' => 'auth']
    $routes->get('propias/(:segment)', 'PublicacionController::propias/$1');
    $routes->get('crear/(:segment)', 'PublicacionController::crear/$1');
    $routes->get('crear', 'PublicacionController::crear'); // Para compatibilidad y redirección
    $routes->post('guardar/(:segment)', 'PublicacionController::guardar/$1');
    $routes->get('editar/(:num)', 'PublicacionController::editar/$1');
    $routes->post('actualizar/(:num)', 'PublicacionController::actualizar/$1');
    $routes->get('eliminar/(:num)', 'PublicacionController::eliminar/$1');
});

// ═══════════════════════════════════════════════════════════════
// APIs REST (ApiController)
// ═══════════════════════════════════════════════════════════════

$routes->get('api/materias', 'ApiController::materias');
$routes->get('api/materias/(:segment)', 'ApiController::materias/$1');
$routes->get('api/tipos', 'ApiController::tipos');
$routes->get('api/tipos/(:num)', 'ApiController::tipos/$1');
$routes->get('api/acuerdos', 'ApiController::acuerdos');
$routes->get('api/acuerdos/(:num)', 'ApiController::acuerdos/$1');

// Publicaciones
$routes->get('api/publicaciones', 'ApiController::publicacionesUsuario');
$routes->get('api/publicaciones/(:segment)', 'ApiController::publicacionesUsuario/$1');
$routes->get('api/publicaciones/(:num)', 'ApiController::publicacion/$1');
$routes->get('api/publicaciones/materia/(:num)', 'ApiController::porMateria/$1');
$routes->get('api/publicaciones/materia/(:num)/(:segment)', 'ApiController::porMateria/$1/$2');

// APIs para datos de formularios
$routes->get('api/tipos_recurso', 'ApiController::tipos_recurso');
$routes->get('api/formatos', 'ApiController::formatos');


// ═══════════════════════════════════════════════════════════════
// COMPATIBILIDAD HACIA ATRÁS (rutas antiguas)
// ═══════════════════════════════════════════════════════════════
$routes->get('auth/cerrar_sesion', 'AuthController::logout'); // Alias para logout
$routes->get('publicaciones/nueva', 'PublicacionController::crear');
$routes->get('inicio/registro', 'AuthController::registro_vista');
$routes->post('inicio/procesar_registro', 'AuthController::procesar_registro');


$routes->get('materiales/buscar', 'BuscadorController::buscar'); // Búsqueda general
$routes->get('materiales/buscar/(:segment)', 'BuscadorController::buscar/$1'); // Búsqueda con término clave en URL

$routes->get('materiales/explorar', 'BuscadorController::explorar'); // Exploración general
$routes->get('materiales/explorar/(:segment)', 'BuscadorController::explorar/$1'); // Exploración con tipo en URL

$routes->get('publicaciones/explorar', 'BuscadorController::explorar'); // Alias para exploración
$routes->get('publicaciones/explorar/(:segment)', 'BuscadorController::explorar/$1'); // Alias con tipo en URL

// Ruta para abrir/descargar el PDF en otra pestaña
$routes->get('publicaciones/descargar/(:num)', 'DescargarController::descargar/$1');

// Ruta para procesar el formulario de pago simulado 
$routes->post('publicaciones/pagar/(:num)', 'PagoController::procesarPago/$1');