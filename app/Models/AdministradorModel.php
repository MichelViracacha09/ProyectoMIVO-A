<?php

namespace App\Models;

use CodeIgniter\Model;

class AdministradorModel extends Model
{
    protected $table         = 'administrador';
    protected $primaryKey    = 'id_administrador';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'nombre_completo', 'usuario_login', 'correo', 'permisos_asignados', 'ultimo_acceso', 'id_usuario'
    ];
    protected $useTimestamps = false;
}
