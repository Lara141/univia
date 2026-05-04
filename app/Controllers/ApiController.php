<?php

namespace App\Controllers;

use App\Models\PublicacionModel;

/**
 * Controlador de APIs REST
 * Retorna datos en formato JSON
 */
class ApiController extends BaseController
{
    /**
     * GET /api/materias
     * Retorna todas las materias disponibles
     */
    public function materias()
    {
        $db = \Config\Database::connect();
        $materias = $db->table('materia')->get()->getResultArray();

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
        $db = \Config\Database::connect();
        $builder = $db->table('publicacion p');

        $builder->select('p.*, m.nombre_materia, a.nombre_archivo, a.ruta');
        $builder->join('materia m', 'm.id_materia = p.id_materia', 'left');
        $builder->join('archivo a', 'a.id_archivo = p.id_archivo', 'left');
        $builder->where('p.id_publicacion', $id);
        $builder->where('p.estado', 1);

        $publicacion = $builder->get()->getRowArray();

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
        $db = \Config\Database::connect();
        $builder = $db->table('publicacion p');

        $builder->select('p.*, m.nombre_materia, a.nombre_archivo, a.ruta');
        $builder->join('materia m', 'm.id_materia = p.id_materia', 'left');
        $builder->join('archivo a', 'a.id_archivo = p.id_archivo', 'left');
        $builder->where('p.dni_usuario', $dni);
        $builder->orderBy('p.fecha_publicacion', 'DESC');

        $publicaciones = $builder->get()->getResultArray();

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
        $db = \Config\Database::connect();
        $builder = $db->table('publicacion p');

        $builder->select('p.*, m.nombre_materia, a.nombre_archivo, a.ruta');
        $builder->join('materia m', 'm.id_materia = p.id_materia', 'left');
        $builder->join('archivo a', 'a.id_archivo = p.id_archivo', 'left');
        $builder->where('p.id_materia', $idMateria);
        $builder->where('p.estado', 1);
        $builder->orderBy('p.fecha_publicacion', 'DESC');

        $publicaciones = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'data' => $publicaciones,
            'count' => count($publicaciones),
        ]);
    }
}
