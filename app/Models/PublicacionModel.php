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
}