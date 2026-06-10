<?php
namespace App\Models;
use CodeIgniter\Model;

/**
 * ═══════════════════════════════════════════════════════════════
 * MODELO: PUBLICACIÓN
 * ═══════════════════════════════════════════════════════════════
 * 
 * Gestión de publicaciones de materiales de la plataforma
 * 
 * Tabla: publicacion
 * Clave primaria: id_publicacion
 *  
 * Relaciones:
 *   - usuario (dni_usuario)
 *   - materia (id_materia)
 *   - archivo (id_archivo)
 * 
 * @author Sistema Univia
 * @package App\Models
 */
class PublicacionModel extends Model {
    /** @var string Tabla de la base de datos */
    protected $table = 'publicacion';
    
    /** @var string Clave primaria */
    protected $primaryKey = 'id_publicacion';
    
    /**
     * Campos permitidos para insertar/actualizar
     * @var array
     */
    protected $allowedFields = [
        'titulo', 'descripcion', 'id_tipo_recurso', 'tipo_acuerdo', 'precio',
        'fecha_publicacion', 'estado', 'dni_usuario', 'id_materia', 'id_archivo',
    ];

    /**
     * Obtiene las publicaciones de un usuario específico
     * 
     * Realiza joins con las tablas materia y archivo para obtener
     * información completa de cada publicación.
     *
     * @param string $dni DNI del usuario propietario
     * @param bool $soloActivas true = solo activas (estado=1), false = todas
     * @return array Array de publicaciones con datos completos
     */
    public function obtenerPublicacionesUsuario(string $dni, bool $soloActivas = true): array
    {
        $builder = $this->builder();
        $builder->select('p.*, m.nombre_materia, a.nombre_archivo as file_name, a.ruta');
        $builder->select('p.*, m.nombre_materia, a.nombre_archivo as file_name, a.ruta, tr.slug as tipo_recurso, f.slug as formato_slug');
        $builder->from('publicacion p');
        $builder->join('materia m', 'm.id_materia = p.id_materia', 'left');
        $builder->join('archivo a', 'a.id_archivo = p.id_archivo', 'left');
        $builder->join('formato f', 'f.id_formato = a.id_formato', 'left');
        $builder->join('tipo_recurso tr', 'tr.id_tipo_recurso = p.id_tipo_recurso', 'left');
        $builder->where('p.dni_usuario', $dni);
  
        if ($soloActivas) {
            $builder->where('p.estado', 1);
        }  

        $builder->orderBy('p.fecha_publicacion', 'DESC');

        return $builder->get()->getResultArray();
    }
}