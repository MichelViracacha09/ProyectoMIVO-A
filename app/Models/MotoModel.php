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

    /**
     * Listado con búsqueda (placa, línea, dueño) y filtro por cliente. Usar con ->paginate().
     * $idCliente restringe a un cliente (perfil Cliente).
     */
    public function filtrar(array $f, ?int $idCliente = null): static
    {
        $this->select('moto.*, cliente.nombre_completo as cliente_nombre, cliente.numero_identificacion')
            ->join('cliente', 'cliente.id_cliente = moto.id_cliente');

        if ($idCliente !== null) {
            $this->where('moto.id_cliente', $idCliente);
        } elseif (!empty($f['id_cliente'])) {
            $this->where('moto.id_cliente', (int) $f['id_cliente']);
        }

        if (!empty($f['placa'])) {
            $this->like('moto.placa', $f['placa']);
        }
        if (!empty($f['q'])) {
            $this->groupStart()
                ->like('moto.placa', $f['q'])
                ->orLike('moto.linea', $f['q'])
                ->orLike('cliente.nombre_completo', $f['q'])
                ->groupEnd();
        }

        return $this->orderBy('moto.id_moto', 'DESC');
    }

    /**
     * Una moto con los datos de su propietario.
     */
    public function getDetalle(int $idMoto, ?int $idCliente = null): ?array
    {
        $this->select('moto.*, cliente.nombre_completo as cliente_nombre, cliente.numero_celular as cliente_celular,
                cliente.correo_electronico as cliente_correo')
            ->join('cliente', 'cliente.id_cliente = moto.id_cliente')
            ->where('moto.id_moto', $idMoto);

        if ($idCliente !== null) {
            $this->where('moto.id_cliente', $idCliente);
        }

        return $this->first();
    }

    /**
     * Servicios asignados a la moto (tabla moto_servicio).
     */
    public function getServicios(int $idMoto): array
    {
        return $this->db->table('moto_servicio')
            ->select('moto_servicio.fecha_asignacion, servicio.nombre_servicio, servicio.precio_servicio')
            ->join('servicio', 'servicio.id_servicio = moto_servicio.id_servicio')
            ->where('moto_servicio.id_moto', $idMoto)
            ->orderBy('moto_servicio.fecha_asignacion', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Historial de servicios de la moto. historial_de_servicios solo guarda la placa como texto.
     */
    public function getHistorial(string $placa): array
    {
        return $this->db->table('historial_de_servicios')
            ->where('placa_moto', $placa)
            ->orderBy('fecha', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * ¿Tiene servicios asignados o historial? (impide eliminarla).
     */
    public function tieneRelaciones(array $moto): bool
    {
        $servicios = $this->db->table('moto_servicio')->where('id_moto', $moto['id_moto'])->countAllResults();
        $historial = $this->db->table('historial_de_servicios')->where('placa_moto', $moto['placa'])->countAllResults();

        return ($servicios + $historial) > 0;
    }
}
