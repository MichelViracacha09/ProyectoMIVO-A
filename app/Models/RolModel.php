<?php

namespace App\Models;

use CodeIgniter\Model;

class RolModel extends Model
{
    protected $table         = 'rol';
    protected $primaryKey    = 'id_rol';
    protected $returnType    = 'array';
    protected $allowedFields = ['nombre', 'descripcion'];
    protected $useTimestamps = false;

    /**
     * Roles con la cantidad de usuarios asociados.
     */
    public function getAllWithUsuarios(): array
    {
        return $this->select('rol.*, COUNT(usuario.id_usuario) AS total_usuarios')
            ->join('usuario', 'usuario.id_rol = rol.id_rol', 'left')
            ->groupBy('rol.id_rol')
            ->orderBy('rol.id_rol', 'ASC')
            ->findAll();
    }

    public function contarUsuarios(int $idRol): int
    {
        return $this->db->table('usuario')->where('id_rol', $idRol)->countAllResults();
    }
}
