<?php

namespace App\Models;

use CodeIgniter\Model;

class ModuloModel extends Model
{
    protected $table = 'modulo';

    /**
     * Devuelve los módulos permitidos para un rol específico, con sus permisos.
     */
    public function getModulosByRol(int $idRol): array
    {
        return $this->select('modulo.id_modulo, modulo.nombre, modulo.ruta, modulo.icono,
                modulo_rol.puede_crear, modulo_rol.puede_editar, modulo_rol.puede_eliminar')
            ->join('modulo_rol', 'modulo_rol.id_modulo = modulo.id_modulo')
            ->where('modulo_rol.id_rol', $idRol)
            ->where('modulo_rol.puede_ver', 1)
            ->where('modulo.activo', 1)
            ->orderBy('modulo.id_modulo', 'ASC')
            ->findAll();
    }
}