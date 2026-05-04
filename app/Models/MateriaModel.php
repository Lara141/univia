<?php

namespace App\Models;
use CodeIgniter\Model;

class MateriaModel extends Model {
    protected $table = 'materia';
    protected $primaryKey = 'id_materia';
    protected $allowedFields = ['nombre_materia'];
}