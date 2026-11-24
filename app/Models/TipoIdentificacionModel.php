<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoIdentificacionModel extends Model
{
    protected $table         = 'tipo_identificacion';
    protected $primaryKey    = 'id_identificacion';
    protected $returnType    = 'array';
    protected $allowedFields = ['nombre', 'abreviatura', 'descripcion'];
    protected $useTimestamps = false;
}
