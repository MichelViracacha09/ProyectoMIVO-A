<?php

namespace App\Models;

use CodeIgniter\Model;

class EstadoClienteModel extends Model
{
    protected $table         = 'estado_cliente';
    protected $primaryKey    = 'id_estado_cliente';
    protected $returnType    = 'array';
    protected $allowedFields = ['nombre', 'descripcion', 'estado'];
    protected $useTimestamps = false;
}
