<?php

namespace App\Services;

use Config\Database;

/**
 * Servicio de Catálogos
 * Encapsula la obtención de datos de catálogo como materias
 */
class CatalogoService
{
    public function obtenerMaterias(): array
    {
        $db = Database::connect();
        return $db->table('materia')->orderBy('nombre_materia', 'ASC')->get()->getResultArray();
    }
}
