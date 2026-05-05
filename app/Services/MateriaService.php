<?php

namespace App\Services;

use Config\Database;


class MateriaService
{
    public function obtenerMaterias(): array
    {
        $db = Database::connect();
        return $db->table('materia')->orderBy('nombre_materia', 'ASC')->get()->getResultArray();
    }
}
