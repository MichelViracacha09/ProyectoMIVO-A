<?php

namespace App\Models;

use CodeIgniter\Model;

class ServicioModel extends Model
{
    protected $table         = 'servicio';
    protected $primaryKey    = 'id_servicio';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'nombre_servicio', 'descripcion_servicio', 'precio_servicio',
        'duracion_estimada', 'garantia_servicio', 'nivel_complejidad', 'id_tipo_servicio'
    ];
    protected $useTimestamps = false;

    /**
     * Todos los servicios, con el nombre de su tipo.
     */
    public function getAllWithTipo(): array
    {
        return $this->select('servicio.*, tipo_servicio.nombre as tipo_nombre')
            ->join('tipo_servicio', 'tipo_servicio.id_tipo_servicio = servicio.id_tipo_servicio')
            ->orderBy('servicio.id_servicio', 'ASC')
            ->findAll();
    }

    /**
     * Listado con búsqueda (nombre, descripción) y filtro por tipo. Usar con ->paginate().
     */
    public function filtrar(array $f): static
    {
        $this->select('servicio.*, tipo_servicio.nombre as tipo_nombre')
            ->join('tipo_servicio', 'tipo_servicio.id_tipo_servicio = servicio.id_tipo_servicio');

        if (!empty($f['q'])) {
            $this->groupStart()
                ->like('servicio.nombre_servicio', $f['q'])
                ->orLike('servicio.descripcion_servicio', $f['q'])
                ->groupEnd();
        }
        if (!empty($f['id_tipo_servicio'])) {
            $this->where('servicio.id_tipo_servicio', (int) $f['id_tipo_servicio']);
        }

        return $this->orderBy('servicio.id_servicio', 'ASC');
    }

    public function getDetalle(int $id): ?array
    {
        return $this->select('servicio.*, tipo_servicio.nombre as tipo_nombre')
            ->join('tipo_servicio', 'tipo_servicio.id_tipo_servicio = servicio.id_tipo_servicio')
            ->where('servicio.id_servicio', $id)
            ->first();
    }

    /**
     * ¿Existe otro servicio con el mismo nombre y tipo? (evita duplicados)
     */
    public function existeDuplicado(string $nombre, int $idTipo, ?int $excluirId = null): bool
    {
        $this->where('LOWER(nombre_servicio)', mb_strtolower(trim($nombre)))
            ->where('id_tipo_servicio', $idTipo);
        if ($excluirId !== null) {
            $this->where('id_servicio !=', $excluirId);
        }

        return $this->countAllResults() > 0;
    }

    /**
     * Cantidad de citas y de motos que usan el servicio (impide eliminarlo).
     */
    public function contarUsos(int $id): int
    {
        return $this->db->table('citas')->where('id_servicio', $id)->countAllResults()
            + $this->db->table('moto_servicio')->where('id_servicio', $id)->countAllResults();
    }
}
