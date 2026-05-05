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
     * @return array Array de materias con sus datos completos
     */
    public function obtenerMaterias(): array
    {
        $db = Database::connect();
        return $db->table('materia')->orderBy('nombre_materia', 'ASC')->get()->getResultArray();
    }
}
