<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\BuscadorService;
use App\Services\PagoService;

/**  
 * Controlador de búsqueda y exploración de materiales.
 *
 * Responsable de recibir filtros desde la interfaz, buscar publicaciones
 * activas y determinar si el usuario ya pagó por recursos de pago.
 * 
 * @package App\Controllers
 */
class BuscadorController extends BaseController
{
    /** @var BuscadorService Servicio para la lógica de búsqueda. */
    protected BuscadorService $buscadorService;

    /** @var PagoService Servicio para la lógica de negocio de pagos. */
    protected PagoService $pagoService;

    /** @var AuthService Servicio para la autenticación y gestión de sesiones. */
    protected AuthService $authService;

    /**
     * Constructor que inicializa los servicios de búsqueda, pago y autenticación.
     */
    public function __construct()
    {
        $this->buscadorService = new BuscadorService();
        $this->pagoService = new PagoService();
        $this->authService = new AuthService();
    }

    /**
     * Busca publicaciones según filtros proporcionados
     * 
     * Filtros disponibles (vía GET):
     *   - q: palabra clave para buscar en título y descripción
     *   - materia: ID de materia
     *   - tipo: tipo de recurso  
     * 
     * Solo retorna publicaciones activas (estado = 1)
     * 
     * @param string|null $palabra_clave_url Palabra clave opcional proveniente de un segmento de URL.
     * @return string|\CodeIgniter\HTTP\RedirectResponse Renderiza la vista de resultados o redirige si no está logueado.
     */
    public function buscar($palabra_clave_url = null)
    {
        if (!$this->authService->estaLogueado()) {
            return redirect()->to('/');
        }

        $usuario_autenticado = $this->authService->getUsuarioAutenticado();

        // Lógica de filtro: Prioriza el parámetro GET 'q', pero si no existe, usa el de la URL.
        $palabra_clave = $this->request->getGet('q') ?? $palabra_clave_url;

        $filtros = [
            'palabra_clave' => $palabra_clave,
            'materia' => $this->request->getGet('materia'),
            'tipo' => $this->request->getGet('tipo'),
        ];
 
        $resultados = $this->buscadorService->buscarPublicaciones($filtros);

        return view('resultados_busqueda', [
            'usuario' => $usuario_autenticado,
            'resultados' => $resultados,
            'filtros' => $filtros // Pasamos los filtros a la vista para que sepa qué se buscó
        ]);
    }

    /**
     * Muestra la pantalla de exploración de materiales.
     *
     * Obtiene los filtros enviados por el usuario, consulta las publicaciones que cumplen dichos criterios
     * y envía los resultados a la vista `explorar_materiales`.
     *
     * @param string|null $tipo_recurso_url Tipo de recurso opcional proveniente de un segmento de URL.
     * @return string|\CodeIgniter\HTTP\RedirectResponse Renderiza la vista de exploración o redirige si no está logueado.
     */
    public function explorar($tipo_recurso_url = null)
    {
        if (!$this->authService->estaLogueado()) {
            return redirect()->to('/');
        }

        $usuario_autenticado = $this->authService->getUsuarioAutenticado();

        // Lógica de filtro: Prioriza el parámetro GET 'tipo', pero si no existe, usa el de la URL.
        $tipo_recurso = $this->request->getGet('tipo') ?? $tipo_recurso_url;
 
        $filtros = [
            'palabra_clave' => $this->request->getGet('q'),
            'materia'       => $this->request->getGet('materia'),
            'tipo'          => $tipo_recurso,
            'acuerdo'       => $this->request->getGet('acuerdo'),
            'formato'       => $this->request->getGet('formato')
        ]; 

        $publicaciones = $this->buscadorService->buscarPublicaciones($filtros);

        $dni = $usuario_autenticado['dni_usuario'];
        // Verificamos de forma histórica contra la tabla 'pago' usando el DNI ya validado.
        foreach ($publicaciones as &$pub) {
            $pub['ya_pagado'] = $this->pagoService->verificarPagoExistente($dni, (int) $pub['id_publicacion']);
        }

        return view('explorar_materiales', [
            'usuario'       => $usuario_autenticado,
            'publicaciones' => $publicaciones,
            'filtros'       => $filtros
        ]);
    }
} 