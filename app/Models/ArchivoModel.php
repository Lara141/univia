<?php
namespace App\Models;
use CodeIgniter\Model;

class ArchivoModel extends Model {
    protected $table = 'archivo';
    protected $primaryKey = 'id_archivo';
    protected $allowedFields = ['nombre_archivo', 'ruta', 'formato'];
}