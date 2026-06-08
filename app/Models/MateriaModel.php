<?php

namespace App\Models;
use CodeIgniter\Model;

/**
 * ═══════════════════════════════════════════════════════════════
 * MODELO: MATERIA
 * ═══════════════════════════════════════════════════════════════
 * 
 * Gestión de materias/asignaturas de la plataforma
 * 
 * Tabla: materia
 * Clave primaria: id_materia
 *  
 * @author Sistema Univia
 * @package App\Models
 */  
class MateriaModel extends Model {
    /** @var string Tabla de la base de datos */
    protected $table = 'materia';
    
    /** @var string Clave primaria */
    protected $primaryKey = 'id_materia';
    
    /**
     * Campos permitidos para insertar/actualizar
     * @var array
     */
    protected $allowedFields = [
        'nombre_materia'  // Nombre de la materia
    ];
}