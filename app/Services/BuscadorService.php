<?php

namespace App\Services;

use Config\Database;

/**
 */
class BuscadorService
{
 /**
     * Obtiene todas las publicaciones activas de una materia específica
     *
     * @param int $idMateria ID de la materia
     * @return array Array de publicaciones ordenadas por fecha descendente
     */
    public function obtenerPublicacionesPorMateria(int $idMateria): array
    {
       $builder = $this->publicacionModel->builder();

        $builder->select('publicacion.*, m.nombre_materia, a.nombre_archivo as file_name, a.ruta, a.formato');

        $builder->join('materia m', 'm.id_materia = publicacion.id_materia', 'left');
        $builder->join('archivo a', 'a.id_archivo = publicacion.id_archivo', 'left');

        $builder->where('publicacion.id_materia', $idMateria);
        $builder->where('publicacion.estado', 1);

        $builder->orderBy('publicacion.fecha_publicacion', 'DESC');

        return $builder->get()->getResultArray();
    }

}
