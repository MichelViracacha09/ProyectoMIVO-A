<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoServicioModel extends Model
{
    protected $table         = 'tipo_servicio';
    protected $primaryKey    = 'id_tipo_servicio';
    protected $returnType    = 'array';
    protected $allowedFields = ['nombre', 'descripcion'];
    protected $useTimestamps = false;
}
