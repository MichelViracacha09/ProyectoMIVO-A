<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table         = 'cliente';
    protected $primaryKey    = 'id_cliente';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'nombre_completo', 'numero_identificacion', 'numero_celular',
        'correo_electronico', 'direccion', 'fecha_nacimiento',
        'id_estado_cliente', 'id_identificacion', 'id_usuario'
    ];

    protected $useTimestamps = false;
}