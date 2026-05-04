<?php

namespace App\Controllers;

use App\Models\ArchivoModel;
use App\Services\CatalogoService;
use App\Services\ArchivoService;
use App\Services\PublicacionService;

/**
 * Controlador de APIs REST
 * Retorna datos en formato JSON
 */
class ApiController extends BaseController
{
    private CatalogoService $catalogoService;
    private PublicacionService $publicacionService;

    public function __construct()
    {
        $archivoService = new ArchivoService(new ArchivoModel());
        $this->catalogoService = new CatalogoService();
        $this->publicacionService = new PublicacionService($archivoService);
    }

    /**
     * GET /api/materias
     * Retorna todas las materias disponibles
     */
    public function materias()
    {
        $materias = $this->catalogoService->obtenerMaterias();

        return $this->response->setJSON([
            'success' => true,
            'data' => $materias,
        ]);
    }

    /**
     * GET /api/tipos
     * Retorna tipos de recursos disponibles
     */
    public function tipos()
    {
        $tipos = [
            ['id' => 1, 'nombre' => 'Apunte'],
            ['id' => 2, 'nombre' => 'Resumen'],
            ['id' => 3, 'nombre' => 'Examen']
        ];

        return $this->response->setJSON([
            'success' => true,
            'data' => $tipos,
        ]);
    }

    /**
     * GET /api/acuerdos
     * Retorna tipos de acuerdos disponibles
     */
    public function acuerdos()
    {
        $acuerdos = [
            ['id' => 1, 'nombre' => 'Gratis'],
            ['id' => 2, 'nombre' => 'Pago']
        ];

        return $this->response->setJSON([
            'success' => true,
            'data' => $acuerdos,
        ]);
    }

    /**
     * GET /api/publicaciones/:id
     * Obtiene una publicación específica
     */
    public function publicacion($id)
    {
        $publicacion = $this->publicacionService->obtenerPublicacionPorId((int) $id);

        if (!$publicacion) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Publicación no encontrada',
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $publicacion,
        ]);
    }

    /**
     * GET /api/publicaciones
     * Obtiene publicaciones del usuario autenticado
     */
    public function publicacionesUsuario()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Usuario no autenticado',
            ])->setStatusCode(401);
        }

        $dni = session()->get('usuario')['dni_usuario'];
        $publicaciones = $this->publicacionService->obtenerPublicacionesUsuario($dni);

        return $this->response->setJSON([
            'success' => true,
            'data' => $publicaciones,
            'count' => count($publicaciones),
        ]);
    }

    /**
     * GET /api/publicaciones/materia/:idMateria
     * Obtiene publicaciones de una materia específica
     */
    public function porMateria($idMateria)
    {
        $publicaciones = $this->publicacionService->obtenerPublicacionesPorMateria((int) $idMateria);

        return $this->response->setJSON([
            'success' => true,
            'data' => $publicaciones,
            'count' => count($publicaciones),
        ]);
    }
}
