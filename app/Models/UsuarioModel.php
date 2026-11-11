<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table         = 'usuario';
    protected $primaryKey    = 'id_usuario';
    protected $returnType    = 'array';
    protected $allowedFields = ['hash', 'estado', 'id_rol'];

    protected $useTimestamps = false;

    /**
     * Busca un usuario por correo, ya sea que esté registrado como
     * administrador o como cliente, y trae su rol y datos de perfil.
     */
    public function getUserByEmail(string $correo): ?array
    {
        return $this->select('
                usuario.id_usuario, usuario.hash, usuario.estado, usuario.id_rol,
                rol.nombre as rol_nombre,
                administrador.id_administrador, administrador.nombre_completo as admin_nombre, administrador.correo as admin_correo,
                cliente.id_cliente, cliente.nombre_completo as cliente_nombre, cliente.correo_electronico as cliente_correo
            ')
            ->join('rol', 'rol.id_rol = usuario.id_rol')
            ->join('administrador', 'administrador.id_usuario = usuario.id_usuario', 'left')
            ->join('cliente', 'cliente.id_usuario = usuario.id_usuario', 'left')
            ->groupStart()
                ->where('administrador.correo', $correo)
                ->orWhere('cliente.correo_electronico', $correo)
            ->groupEnd()
            ->first();
    }
}