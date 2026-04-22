<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/*
Clase base abstracta de la cual heredan todos los controladores de la aplicación. 
Centraliza configuraciones comunes para evitar repetir código.
*/

abstract class BaseController extends Controller
{
    /*Define los helpers que se cargan automáticamente en todos los controladores que extienden esta clase.*/
    protected $helpers = ['form', 'url', 'session'];

    /* 
    Método que se ejecuta automáticamente al inicializar cualquier controlador que herede de BaseController.
    */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
       
        parent::initController($request, $response, $logger);

    }

}
