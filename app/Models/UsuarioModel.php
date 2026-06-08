<?php
namespace App\Models;
use CodeIgniter\Model;

/**
 * ═══════════════════════════════════════════════════════════════
 * MODELO: USUARIO
 * ═══════════════════════════════════════════════════════════════
 * 
 * Gestión de datos de usuarios de la plataforma
 * 
 * Tabla: usuario
 * Clave primaria: dni_usuario
 * 
 * @author Sistema Univia
 * @package App\Models
 */
class UsuarioModel extends Model
{
    /** @var string Tabla de la base de datos */
    protected $table = 'usuario';
    
    /** @var string Clave primaria de la tabla */
    protected $primaryKey = 'dni_usuario';
  
    /** 
     * Campos permitidos para insertar/actualizar
     * @var array
     */
    protected $allowedFields = [
        'dni_usuario',          // DNI único del usuario
        'correo',              // Email único
        'contrasena',          // Contraseña (encriptada)
        'fecha_registro',      // Fecha de registro
        'estado',              // 1 = activo, 0 = inactivo
        'Nombre_usuario',      // Nombre del usuario
        'Apellido_usuario',    // Apellido del usuario
        'id_carrera'           // Relación con tabla carrera
    ];
}