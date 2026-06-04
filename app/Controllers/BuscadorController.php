<?php

namespace App\Controllers;


use App\Services\BuscadorService;


class BuscadorController extends BaseController
{
    protected BuscadorService $buscadorService;
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
     * @return \CodeIgniter\HTTP\Response Vista con resultados de búsqueda
     */
    public function buscar()
    {
        $filtros = [
            'palabra_clave' => $this->request->getGet('q'),
            'materia' => $this->request->getGet('materia'),
            'tipo' => $this->request->getGet('tipo'),
        ];

        $resultados = $this->publicacionService->buscarPublicaciones($filtros);

        return view('resultados_busqueda', [
            'usuario' => session()->get('usuario'),
            'resultados' => $resultados,
        ]);
    }

    /**
     * Muestra la pantalla de exploración de materiales.
     *
     * Obtiene los filtros enviados por el usuario, consulta las publicaciones que cumplen dichos criterios
     * y envía los resultados a la vista explorar_materiales.
     *
     * @return mixed
     */
    public function explorar()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        $filtros = [
            'palabra_clave' => $this->request->getGet('q'),
            'materia'       => $this->request->getGet('materia'),
            'tipo'          => $this->request->getGet('tipo'),
            'acuerdo'       => $this->request->getGet('acuerdo'),
            'formato'       => $this->request->getGet('formato')
        ];

        $publicaciones = $this->publicacionService->buscarPublicaciones($filtros);
        $dni = session()->get('usuario')['dni_usuario'];

        // REGLA DE TRAZABILIDAD: Verificamos de forma histórica contra la tabla 'pago'
        foreach ($publicaciones as &$pub) {
            $pub['ya_pagado'] = $this->publicacionService->verificarPagoExistente($dni, (int)$pub['id_publicacion']);
        }

        return view('explorar_materiales', [
            'usuario'       => session()->get('usuario'),
            'publicaciones' => $publicaciones,
            'filtros'       => $filtros
        ]);
    }
}