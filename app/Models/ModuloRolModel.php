<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Permisos por rol y módulo (tabla modulo_rol).
 */
class ModuloRolModel extends Model
{
    protected $table         = 'modulo_rol';
    protected $primaryKey    = 'id_modulo_rol';
    protected $returnType    = 'array';
    protected $allowedFields = ['id_modulo', 'id_rol', 'puede_ver', 'puede_crear', 'puede_editar', 'puede_eliminar'];
    protected $useTimestamps = false;

    public const ACCIONES = ['ver', 'crear', 'editar', 'eliminar'];

    /**
     * Permiso de un rol sobre un módulo (por su "ruta"). Solo módulos activos.
     */
    public function getPermiso(int $idRol, string $rutaModulo): ?array
    {
        return $this->select('modulo_rol.*')
            ->join('modulo', 'modulo.id_modulo = modulo_rol.id_modulo')
            ->where('modulo_rol.id_rol', $idRol)
            ->where('modulo.ruta', $rutaModulo)
            ->where('modulo.activo', 1)
            ->first();
    }

    /**
     * Todos los módulos con los permisos actuales del rol (0 si no tiene fila).
     */
    public function getMatrizByRol(int $idRol): array
    {
        return $this->db->table('modulo')
            ->select('modulo.id_modulo, modulo.nombre, modulo.ruta, modulo.icono, modulo.activo,
                COALESCE(modulo_rol.puede_ver, 0) AS puede_ver,
                COALESCE(modulo_rol.puede_crear, 0) AS puede_crear,
                COALESCE(modulo_rol.puede_editar, 0) AS puede_editar,
                COALESCE(modulo_rol.puede_eliminar, 0) AS puede_eliminar', false)
            ->join('modulo_rol', 'modulo_rol.id_modulo = modulo.id_modulo AND modulo_rol.id_rol = ' . (int) $idRol, 'left')
            ->orderBy('modulo.id_modulo', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Guarda la matriz completa de un rol: actualiza o inserta una fila por módulo.
     *
     * @param array<int, array<string, int>> $matriz [id_modulo => [puede_ver=>0|1, ...]]
     */
    public function guardarMatriz(int $idRol, array $matriz): bool
    {
        $this->db->transStart();

        foreach ($matriz as $idModulo => $permisos) {
            $fila = [
                'puede_ver'      => (int) !empty($permisos['puede_ver']),
                'puede_crear'    => (int) !empty($permisos['puede_crear']),
                'puede_editar'   => (int) !empty($permisos['puede_editar']),
                'puede_eliminar' => (int) !empty($permisos['puede_eliminar']),
            ];

            $existente = $this->where('id_rol', $idRol)->where('id_modulo', (int) $idModulo)->first();
            if ($existente) {
                $this->update($existente['id_modulo_rol'], $fila);
            } elseif (array_sum($fila) > 0) {
                $this->insert($fila + ['id_rol' => $idRol, 'id_modulo' => (int) $idModulo]);
            }
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }
}
