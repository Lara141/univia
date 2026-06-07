<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
 
/**  
 *Controlador base
 * 
 * Clase abstracta que heredan todos los controladores de la aplicación.
 * 
 * Responsabilidades:
 *   - Cargar helpers comunes (form, url, session)
 *   - Centralizar configuraciones y metodos reutilizables
 *   - Inicializar request, response y logger
 */
abstract class BaseController extends Controller
{
    /**
     * Helpers cargados automaticamente en todos los controladores
     * 
     * - form: facilita creacion de formularios
     * - url: manejo de rutas (site_url, base_url)
     * - session: acceso a datos de sesión
     * 
     * @var array
     */
    protected $helpers = ['form', 'url', 'session'];

    /**
     * Inicializa el controlador con request, response y logger
     * 
     * Se ejecuta automaticamente al instanciar cualquier controlador
     * que herede de esta clase.
     * 
     * @param RequestInterface $request Objeto de request HTTP
     * @param ResponseInterface $response Objeto de response HTTP
     * @param LoggerInterface $logger Logger para registrar eventos
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
       
        parent::initController($request, $response, $logger);

    }

}
