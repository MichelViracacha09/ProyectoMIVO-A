<?php

namespace App\Models;

use CodeIgniter\Model;

class CitaModel extends Model
{
    protected $table         = 'citas';
    protected $primaryKey    = 'id_citas';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'fecha_cita', 'hora_cita', 'servicio_a_realizar',
        'estado_cita', 'id_cliente', 'id_servicio'
    ];

    protected $useTimestamps = false;

    /**
     * Todas las citas, con nombre del cliente y del servicio (para el Administrador).
     */
    public function getAllWithDetalle(): array
    {
        return $this->select('citas.*, cliente.nombre_completo as cliente_nombre, servicio.nombre_servicio')
            ->join('cliente', 'cliente.id_cliente = citas.id_cliente')
            ->join('servicio', 'servicio.id_servicio = citas.id_servicio')
            ->orderBy('citas.fecha_cita', 'DESC')
            ->findAll();
    }

    /**
     * Solo las citas de un cliente específico, con el nombre del servicio.
     */
    public function getByCliente(int $idCliente): array
    {
        return $this->select('citas.*, servicio.nombre_servicio')
            ->join('servicio', 'servicio.id_servicio = citas.id_servicio')
            ->where('citas.id_cliente', $idCliente)
            ->orderBy('citas.fecha_cita', 'DESC')
            ->findAll();
    }

    /**
     * Una cita puntual, validando que pertenezca al cliente (seguridad).
     */
    public function getCitaForCliente(int $idCita, int $idCliente): ?array
    {
        return $this->where('id_citas', $idCita)
            ->where('id_cliente', $idCliente)
            ->first();
    }
}