<?php

namespace App\Controllers;
use App\Services\AuthService;
 
use App\Models\ArchivoModel;
use App\Services\MateriaService;
use App\Services\ArchivoService;
use App\Services\PublicacionService;
use App\Services\BuscadorService;
 
/**
 * Controlador de APIs REST
 * Retorna datos en formato JSON
 */
class ApiController extends BaseController
{
    private MateriaService $materiaService;
    private PublicacionService $publicacionService;
    private BuscadorService $buscadorService;
    private AuthService $authService;

    public function __construct()
    {
        $archivoService = new ArchivoService(new ArchivoModel());
        $this->materiaService = new MateriaService();
        $this->publicacionService = new PublicacionService($archivoService);
        $this->authService = new AuthService();
        $this->buscadorService = new BuscadorService();
    }

    /**
     * GET /api/materias
     * GET /api/materias/(:segment)
     * Retorna todas las materias disponibles, o las filtra por un término de búsqueda.
     * @param string|null $filtro Término para filtrar materias por nombre.
     */
    public function materias($filtro = null)
    {
        $materias = $this->materiaService->obtenerMaterias();

        if ($filtro) {
            $filtro = strtolower(trim($filtro));
            $materias = array_filter($materias, function ($materia) use ($filtro) {
                return str_contains(strtolower($materia['nombre_materia']), $filtro);
            });
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => array_values($materias),
        ]);
    }

    /**
     * GET /api/tipos
     * GET /api/tipos/(:num)
     * Retorna tipos de recursos disponibles, o uno específico por su ID.
     * @param int|null $id ID del tipo de recurso a obtener.
     */
    public function tipos($id = null)
    {
        $tipos = [
            ['id' => 1, 'nombre' => 'Apunte'],
            ['id' => 2, 'nombre' => 'Resumen'],
            ['id' => 3, 'nombre' => 'Examen']
        ];

        if ($id !== null) {
            $id = (int) $id;
            $tipo_encontrado = null;
            foreach ($tipos as $tipo) {
                if ($tipo['id'] === $id) {
                    $tipo_encontrado = $tipo;
                    break;
                }
            }
            if ($tipo_encontrado) {
                return $this->response->setJSON(['success' => true, 'data' => $tipo_encontrado]);
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Tipo no encontrado'])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $tipos,
        ]);
    }

    /**
     * GET /api/acuerdos
     * GET /api/acuerdos/(:num)
     * Retorna tipos de acuerdos disponibles, o uno específico por su ID.
     * @param int|null $id ID del tipo de acuerdo a obtener.
     */
    public function acuerdos($id = null)
    {
        $acuerdos = [
            ['id' => 1, 'nombre' => 'Gratis'],
            ['id' => 2, 'nombre' => 'Pago']
        ];

        if ($id !== null) {
            $id = (int) $id;
            $acuerdo_encontrado = null;
            foreach ($acuerdos as $acuerdo) {
                if ($acuerdo['id'] === $id) {
                    $acuerdo_encontrado = $acuerdo;
                    break;
                }
            }
            if ($acuerdo_encontrado) {
                return $this->response->setJSON(['success' => true, 'data' => $acuerdo_encontrado]);
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Acuerdo no encontrado'])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $acuerdos,
        ]);
    }

    /**
     * GET /api/publicaciones/:id
     * Obtiene una publicacion especifica
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
     * GET /api/publicaciones/(:segment)
     * Obtiene publicaciones del usuario autenticado. Se puede filtrar por estado.
     * @param string $filtro_estado 'activas' (default) o 'todas'.
     */
    public function publicacionesUsuario($filtro_estado = 'activas')
    {
        if (!$this->authService->estaLogueado()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Usuario no autenticado',
            ])->setStatusCode(401);
        }

        $usuario = $this->authService->getUsuarioAutenticado();
        $dni_usuario = $usuario['dni_usuario'];
        
        // El parámetro se usa para determinar si se muestran solo las activas o todas.
        $soloActivas = ($filtro_estado !== 'todas');
        $publicaciones = $this->publicacionService->obtenerPublicacionesUsuario($dni_usuario, $soloActivas);

        return $this->response->setJSON([
            'success' => true,
            'data' => $publicaciones,
            'count' => count($publicaciones),
        ]);
    } 

    /** 
     * GET /api/publicaciones/materia/:idMateria
     * GET /api/publicaciones/materia/:idMateria/(:segment)
     * Obtiene publicaciones de una materia especifica, con filtro opcional por tipo de recurso.
     * @param int $idMateria ID de la materia.
     * @param string|null $tipo Tipo de recurso para filtrar (e.g., 'resumen', 'examen').
     */
    public function porMateria($idMateria, $tipo = null)
    {
        $filtros = [
            'materia' => (int) $idMateria,
            'tipo' => $tipo,
        ];
        $publicaciones = $this->buscadorService->buscarPublicaciones($filtros);

        return $this->response->setJSON([
            'success' => true,
            'data' => $publicaciones,
            'count' => count($publicaciones),
        ]);
    }

    /**
     * GET /api/tipos_recurso
     * Retorna todos los tipos de recurso disponibles.
     */
    public function tipos_recurso()
    {
        $db = \Config\Database::connect();
        $data = $db->table('tipo_recurso')->orderBy('nombre_tipo', 'ASC')->get()->getResultArray();
        return $this->response->setJSON(['success' => true, 'data' => $data]);
    }

    /**
     * GET /api/formatos
     * Retorna todos los formatos de archivo disponibles.
     */
    public function formatos()
    {
        $db = \Config\Database::connect();
        $data = $db->table('formato')->orderBy('id_formato', 'ASC')->get()->getResultArray();
        return $this->response->setJSON(['success' => true, 'data' => $data]);
    }


}
