<?php
namespace App\Models;
use CodeIgniter\Model;

class PublicacionModel extends Model {
    protected $table = 'publicacion';
    protected $primaryKey = 'id_publicacion';
    // Agregamos 'precio' a la lista
    protected $allowedFields = [
        'titulo', 'descripcion', 'tipo_recurso', 'tipo_acuerdo', 'precio', 
        'fecha_publicacion', 'estado', 'dni_usuario', 'id_materia', 'id_archivo'
    ];

    /**
     * Retorna las publicaciones de un usuario con datos de materia y archivo.
     *
     * @param string $dni DNI del usuario
     * @param bool $soloActivas Filtrar solo publicaciones activas
     * @return array
     */
    public function obtenerPublicacionesUsuario(string $dni, bool $soloActivas = true): array
    {
        $builder = $this->builder();
        $builder->select('p.*, m.nombre_materia, a.nombre_archivo as file_name, a.ruta');
        $builder->from('publicacion p');
        $builder->join('materia m', 'm.id_materia = p.id_materia', 'left');
        $builder->join('archivo a', 'a.id_archivo = p.id_archivo', 'left');
        $builder->where('p.dni_usuario', $dni);

        if ($soloActivas) {
            $builder->where('p.estado', 1);
        }

        $builder->orderBy('p.fecha_publicacion', 'DESC');

        return $builder->get()->getResultArray();
    }
}