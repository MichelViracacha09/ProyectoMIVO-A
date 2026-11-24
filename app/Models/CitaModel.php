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

    /** Estados definidos en el diccionario de datos (citas.estado_cita). */
    public const ESTADOS = ['PENDIENTE', 'CONFIRMADA', 'COMPLETADA', 'CANCELADA'];

    /** Estados en los que la cita ya no se puede cancelar. */
    public const ESTADOS_FINALES = ['COMPLETADA', 'CANCELADA'];

    /** Máximo de citas activas permitidas en la misma fecha y hora (un taller, una franja). */
    public const MAX_POR_FRANJA = 1;

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

    /**
     * Prepara el listado con búsqueda y filtros. Usar con ->paginate().
     * $idCliente restringe a un cliente (perfil Cliente).
     *
     * Filtros: q, estado, fecha_desde, fecha_hasta, id_cliente.
     */
    public function filtrar(array $f, ?int $idCliente = null): static
    {
        $this->select('citas.*, cliente.nombre_completo as cliente_nombre, cliente.numero_identificacion, servicio.nombre_servicio')
            ->join('cliente', 'cliente.id_cliente = citas.id_cliente')
            ->join('servicio', 'servicio.id_servicio = citas.id_servicio');

        if ($idCliente !== null) {
            $this->where('citas.id_cliente', $idCliente);
        } elseif (!empty($f['id_cliente'])) {
            $this->where('citas.id_cliente', (int) $f['id_cliente']);
        }

        if (!empty($f['q'])) {
            $this->groupStart()
                ->like('cliente.nombre_completo', $f['q'])
                ->orLike('cliente.numero_identificacion', $f['q'])
                ->orLike('servicio.nombre_servicio', $f['q'])
                ->orLike('citas.servicio_a_realizar', $f['q'])
                ->groupEnd();
        }
        if (!empty($f['estado']) && in_array($f['estado'], self::ESTADOS, true)) {
            $this->where('citas.estado_cita', $f['estado']);
        }
        if (!empty($f['fecha_desde'])) {
            $this->where('citas.fecha_cita >=', $f['fecha_desde']);
        }
        if (!empty($f['fecha_hasta'])) {
            $this->where('citas.fecha_cita <=', $f['fecha_hasta']);
        }

        return $this->orderBy('citas.fecha_cita', 'DESC')->orderBy('citas.hora_cita', 'DESC');
    }

    /**
     * Una cita con cliente y servicio (detalle).
     */
    public function getDetalle(int $idCita, ?int $idCliente = null): ?array
    {
        $this->select('citas.*, cliente.nombre_completo as cliente_nombre, cliente.numero_celular as cliente_celular,
                cliente.correo_electronico as cliente_correo, servicio.nombre_servicio, servicio.precio_servicio')
            ->join('cliente', 'cliente.id_cliente = citas.id_cliente')
            ->join('servicio', 'servicio.id_servicio = citas.id_servicio')
            ->where('citas.id_citas', $idCita);

        if ($idCliente !== null) {
            $this->where('citas.id_cliente', $idCliente);
        }

        return $this->first();
    }

    /**
     * ¿Ya se alcanzó el cupo de citas activas en esa fecha y hora?
     */
    public function hayConflicto(string $fecha, string $hora, ?int $excluirId = null): bool
    {
        $this->where('fecha_cita', $fecha)
            ->where('hora_cita', date('H:i:s', strtotime($hora)))
            ->where('estado_cita !=', 'CANCELADA');

        if ($excluirId !== null) {
            $this->where('id_citas !=', $excluirId);
        }

        return $this->countAllResults() >= self::MAX_POR_FRANJA;
    }

    /**
     * Cantidad de citas por estado: ['PENDIENTE' => n, ...]. Opcionalmente de un cliente.
     */
    public function contarPorEstado(?int $idCliente = null): array
    {
        $this->select('estado_cita, COUNT(*) AS total')->groupBy('estado_cita');
        if ($idCliente !== null) {
            $this->where('id_cliente', $idCliente);
        }

        $conteo = array_fill_keys(self::ESTADOS, 0);
        foreach ($this->findAll() as $fila) {
            $conteo[strtoupper($fila['estado_cita'])] = (int) $fila['total'];
        }

        return $conteo;
    }
}
