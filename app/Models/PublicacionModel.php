<?php
namespace App\Models;
use CodeIgniter\Model;

class PublicacionModel extends Model {
    protected $table = 'publicacion';
    protected $primaryKey = 'id_publicacion';
    protected $allowedFields = [
        'titulo', 'descripcion', 'tipo_recurso', 'tipo_acuerdo', 
        'fecha_publicacion', 'estado', 'dni_usuario', 'id_materia', 'id_archivo'
    ];
}
