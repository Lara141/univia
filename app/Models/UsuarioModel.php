<?php
namespace App\Models;
use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table      = 'usuario';
    protected $primaryKey = 'dni_usuario'; // Clave primaria según tu SQL

    protected $allowedFields = [
        'dni_usuario', 'correo', 'contrasena', 'fecha_registro', 
        'estado', 'Nombre_usuario', 'Apellido_usuario', 'id_carrera'
    ];
}