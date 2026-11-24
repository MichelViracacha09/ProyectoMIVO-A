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

    /** usuario.estado (diccionario: activo, inactivo, bloqueado). */
    public const ESTADOS = ['ACTIVO', 'INACTIVO', 'BLOQUEADO'];

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
                cliente.id_cliente, cliente.id_estado_cliente, cliente.nombre_completo as cliente_nombre, cliente.correo_electronico as cliente_correo
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

    private function baseListado(): static
    {
        return $this->select('usuario.id_usuario, usuario.estado, usuario.id_rol, usuario.fecha_creacion, usuario.fecha_actualizacion,
                rol.nombre as rol_nombre,
                administrador.id_administrador, administrador.nombre_completo as admin_nombre,
                administrador.usuario_login, administrador.correo as admin_correo,
                cliente.id_cliente, cliente.nombre_completo as cliente_nombre, cliente.correo_electronico as cliente_correo')
            ->join('rol', 'rol.id_rol = usuario.id_rol')
            ->join('administrador', 'administrador.id_usuario = usuario.id_usuario', 'left')
            ->join('cliente', 'cliente.id_usuario = usuario.id_usuario', 'left');
    }

    /**
     * Listado administrativo con búsqueda (nombre, correo, login) y filtros por rol y estado.
     * Usar con ->paginate().
     */
    public function filtrar(array $f): static
    {
        $this->baseListado();

        if (!empty($f['q'])) {
            $this->groupStart()
                ->like('administrador.nombre_completo', $f['q'])
                ->orLike('administrador.correo', $f['q'])
                ->orLike('administrador.usuario_login', $f['q'])
                ->orLike('cliente.nombre_completo', $f['q'])
                ->orLike('cliente.correo_electronico', $f['q'])
                ->groupEnd();
        }
        if (!empty($f['id_rol'])) {
            $this->where('usuario.id_rol', (int) $f['id_rol']);
        }
        if (!empty($f['estado']) && in_array($f['estado'], self::ESTADOS, true)) {
            $this->where('usuario.estado', $f['estado']);
        }

        return $this->orderBy('usuario.id_usuario', 'ASC');
    }

    public function getDetalle(int $idUsuario): ?array
    {
        return $this->baseListado()->where('usuario.id_usuario', $idUsuario)->first();
    }

    /**
     * Cantidad de administradores activos (para no dejar el sistema sin administrador).
     */
    public function contarAdministradoresActivos(): int
    {
        return $this->db->table('administrador')
            ->join('usuario', 'usuario.id_usuario = administrador.id_usuario')
            ->where('usuario.estado', 'ACTIVO')
            ->countAllResults();
    }
}
