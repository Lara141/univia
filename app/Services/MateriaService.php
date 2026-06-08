<?php

namespace App\Services;

use Config\Database;

/**
 * ═══════════════════════════════════════════════════════════════
 * SERVICIO: GESTIÓN DE MATERIAS
 * ═══════════════════════════════════════════════════════════════
 * 
 * Responsable de:
 *   - Obtener listado de materias
 *   - Ordenamiento de materias
 * 
 * @author Sistema Univia
 * @package App\Services
 */  
class MateriaService
{
    /**
     * Obtiene todas las materias disponibles ordenadas alfabéticamente
     * 
     * @param string $orden La dirección del ordenamiento ('ASC' o 'DESC'). Por defecto es 'ASC'.
     * @return array Array de materias con sus datos completos
     */
    public function obtenerMaterias(string $orden = 'ASC'): array
    {
        $db = Database::connect();
 
        // Se asegura que el valor de orden sea solo ASC o DESC para evitar inyecciones.
        $direccionOrden = (strtoupper($orden) === 'DESC') ? 'DESC' : 'ASC';

        return $db->table('materia')->orderBy('nombre_materia', $direccionOrden)->get()->getResultArray();
    }
}
