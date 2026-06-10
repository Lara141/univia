<?php

namespace App\Services;

use App\Models\PublicacionModel;

/**
 * Servicio encargado de buscar y filtrar publicaciones.
 *
 * Mantiene la lógica de consulta de publicaciones activas, aplica filtros
 * dinámicos y prepara resultados para las vistas de búsqueda y exploración.
 *
 * @package App\Services
 */
class BuscadorService
{ 
    private PublicacionModel $publicacionModel;

    /**
     * Constructor.
     *
     * @param PublicacionModel|null $publicacionModel Modelo de publicación inyectable (pruebas o extensión)
     */
    public function __construct(PublicacionModel $publicacionModel = null)
    {
        $this->publicacionModel = $publicacionModel ?? new PublicacionModel();
    }

 /**
     * Obtiene todas las publicaciones activas de una materia específica
     *
     * @param int $idMateria ID de la materia
     * @return array Array de publicaciones ordenadas por fecha descendente
     */
    public function obtenerPublicacionesPorMateria(int $idMateria): array
    {
       $builder = $this->publicacionModel->builder();

        $builder->select('publicacion.*, m.nombre_materia, a.nombre_archivo as file_name, a.ruta, f.slug as formato');
        $builder->select('publicacion.*, m.nombre_materia, a.nombre_archivo as file_name, a.ruta, f.slug as formato, tr.slug as tipo_recurso');

        $builder->join('materia m', 'm.id_materia = publicacion.id_materia', 'left');
        $builder->join('archivo a', 'a.id_archivo = publicacion.id_archivo', 'left');
        $builder->join('formato f', 'f.id_formato = a.id_formato', 'left');
        $builder->join('tipo_recurso tr', 'tr.id_tipo_recurso = publicacion.id_tipo_recurso', 'left');

        $builder->where('publicacion.id_materia', $idMateria);
        $builder->where('publicacion.estado', 1);

        $builder->orderBy('publicacion.fecha_publicacion', 'DESC');

        return $builder->get()->getResultArray();
    }

        /**
     * Busca publicaciones aplicando múltiples filtros
     * 
     * Filtros soportados:
     *   - palabra_clave: busca en titulo y descripcion (LIKE)
     *   - materia: filtra por id_materia
     *   - tipo: filtra por tipo_recurso
     * 
     * Solo retorna publicaciones activas (estado=1)
     * Resultados ordenados por fecha descendente
     *
     * @param array $filtros Array con claves: palabra_clave, materia, tipo
     * @return array Array de publicaciones que cumplen los criterios
     */
    public function buscarPublicaciones(array $filtros): array
    {
        $builder = $this->publicacionModel->builder();

        $builder->select('publicacion.*, m.nombre_materia, a.nombre_archivo as file_name, a.ruta, f.slug as formato, u.Nombre_usuario, u.Apellido_usuario, tr.slug as tipo_recurso');

        $builder->join('materia m', 'm.id_materia = publicacion.id_materia', 'left');
        $builder->join('archivo a', 'a.id_archivo = publicacion.id_archivo', 'left');
        $builder->join('formato f', 'f.id_formato = a.id_formato', 'left');
        $builder->join('usuario u', 'u.dni_usuario = publicacion.dni_usuario', 'left');
        $builder->join('tipo_recurso tr', 'tr.id_tipo_recurso = publicacion.id_tipo_recurso', 'left');

        // ═══ ¡LLAMADAS a las funciones filtrar y ordenar ═══
        $this->filtrarResultados($builder, $filtros);
        $this->ordenarResultados($builder);

        return $builder->get()->getResultArray();
    }

    /**
     * Aplica todos los filtros dinámicos a la consulta.
     */
    private function filtrarResultados($builder, array $filtros): void
    {
        // Solo publicaciones activas (estado = 1)
        $builder->where('publicacion.estado', 1);

        if (!empty($filtros['palabra_clave'])) {
            $builder->groupStart()
                ->like('publicacion.titulo', $filtros['palabra_clave'])
                ->orLike('publicacion.descripcion', $filtros['palabra_clave'])
            ->groupEnd();
        }

        if (!empty($filtros['materia'])) {
            $builder->where('publicacion.id_materia', $filtros['materia']);
        }

        if (!empty($filtros['tipo'])) {
            $builder->where('tr.slug', $filtros['tipo']);
        } 

        if (!empty($filtros['acuerdo'])) {
            $builder->where('publicacion.tipo_acuerdo', $filtros['acuerdo']);
        }

        if (!empty($filtros['formato'])) {
            $builder->where('f.slug', $filtros['formato']);
        }
    }
  
    /**
     * Establece el criterio de ordenamiento de la consulta (Más recientes primero).
     */ 
    private function ordenarResultados($builder): void
    {
        $builder->orderBy('publicacion.fecha_publicacion', 'DESC');
    }

}
