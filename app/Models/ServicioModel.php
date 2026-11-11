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
}