<?php
namespace App\Models;
use CodeIgniter\Model;

/**
 * ═══════════════════════════════════════════════════════════════
 * MODELO: ARCHIVO
 * ═══════════════════════════════════════════════════════════════
 * 
 * Gestión de archivos adjuntos a las publicaciones
 *  
 * Tabla: archivo
 * Clave primaria: id_archivo
 *   
 * @author Sistema Univia
 * @package App\Models
 */ 
class ArchivoModel extends Model {
    /** @var string Tabla de la base de datos */
    protected $table = 'archivo';
    
    /** @var string Clave primaria */
    protected $primaryKey = 'id_archivo';
    
    /**
     * Campos permitidos para insertar/actualizar
     * @var array
     */
    protected $allowedFields = [
        'nombre_archivo',  // Nombre original del archivo
        'ruta',           // Ruta del archivo en el servidor
        'id_formato'         // Extensión del archivo (pdf, doc, etc)
    ];
}