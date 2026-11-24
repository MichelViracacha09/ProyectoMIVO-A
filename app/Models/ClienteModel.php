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

    /** estado_cliente.id_estado_cliente: 1 = Activo, 2 = Inactivo, 3 = Bloqueado. */
    public const ESTADO_ACTIVO   = 1;
    public const ESTADO_INACTIVO = 2;

    /**
     * Listado con búsqueda (nombre, documento, correo, celular) y filtro por estado. Usar con ->paginate().
     */
    public function filtrar(array $f): static
    {
        $this->select('cliente.*, estado_cliente.nombre as estado_nombre, tipo_identificacion.abreviatura as tipo_doc')
            ->join('estado_cliente', 'estado_cliente.id_estado_cliente = cliente.id_estado_cliente')
            ->join('tipo_identificacion', 'tipo_identificacion.id_identificacion = cliente.id_identificacion');

        if (!empty($f['q'])) {
            $this->groupStart()
                ->like('cliente.nombre_completo', $f['q'])
                ->orLike('cliente.numero_identificacion', $f['q'])
                ->orLike('cliente.correo_electronico', $f['q'])
                ->orLike('cliente.numero_celular', $f['q'])
                ->groupEnd();
        }
        if (!empty($f['id_estado_cliente'])) {
            $this->where('cliente.id_estado_cliente', (int) $f['id_estado_cliente']);
        }

        return $this->orderBy('cliente.nombre_completo', 'ASC');
    }

    public function getDetalle(int $id): ?array
    {
        return $this->select('cliente.*, estado_cliente.nombre as estado_nombre, tipo_identificacion.nombre as tipo_doc')
            ->join('estado_cliente', 'estado_cliente.id_estado_cliente = cliente.id_estado_cliente')
            ->join('tipo_identificacion', 'tipo_identificacion.id_identificacion = cliente.id_identificacion')
            ->where('cliente.id_cliente', $id)
            ->first();
    }

    /**
     * ¿El cliente tiene motos, citas o facturas? (impide eliminarlo; se debe inactivar).
     */
    public function tieneRelaciones(int $id): bool
    {
        foreach (['moto', 'citas', 'facturas'] as $tabla) {
            if ($this->db->table($tabla)->where('id_cliente', $id)->countAllResults() > 0) {
                return true;
            }
        }
        return false;
    }
}
