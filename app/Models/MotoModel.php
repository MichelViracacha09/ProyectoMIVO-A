<?php

namespace App\Models;

use CodeIgniter\Model;

class MotoModel extends Model
{
    protected $table         = 'moto';
    protected $primaryKey    = 'id_moto';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'placa', 'linea', 'modelo', 'color', 'cilindraje', 'kilometraje',
        'tipo_combustible', 'proxima_fecha_mantenimiento', 'soat',
        'numero_tarjeta_propiedad', 'observaciones', 'id_cliente', 'id_tipo_servicio'
    ];

    protected $useTimestamps = false;

    /**
     * Todas las motos, con el nombre del cliente dueño (para el Administrador).
     */
    public function getAllWithCliente(): array
    {
        return $this->select('moto.*, cliente.nombre_completo as cliente_nombre')
            ->join('cliente', 'cliente.id_cliente = moto.id_cliente')
            ->orderBy('moto.id_moto', 'DESC')
            ->findAll();
    }

    /**
     * Solo las motos de un cliente específico.
     */
    public function getByCliente(int $idCliente): array
    {
        return $this->where('id_cliente', $idCliente)
            ->orderBy('id_moto', 'DESC')
            ->findAll();
    }

    /**
     * Una moto puntual, validando que pertenezca al cliente (seguridad).
     */
    public function getMotoForCliente(int $idMoto, int $idCliente): ?array
    {
        return $this->where('id_moto', $idMoto)
            ->where('id_cliente', $idCliente)
            ->first();
    }
}