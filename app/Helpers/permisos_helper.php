<?php

use App\Models\ModuloRolModel;

if (!function_exists('puede')) {
    /**
     * ¿El rol de la sesión tiene permiso sobre el módulo? (según modulo_rol)
     *
     * @param string $modulo Ruta del módulo (modulo.ruta), ej. 'motos'
     * @param string $accion ver | crear | editar | eliminar
     */
    function puede(string $modulo, string $accion = 'ver'): bool
    {
        static $cache = [];

        if (!in_array($accion, ModuloRolModel::ACCIONES, true) || !session()->get('logged_in')) {
            return false;
        }

        $idRol = (int) session()->get('id_rol_num');
        $key   = $idRol . ':' . $modulo;

        if (!array_key_exists($key, $cache)) {
            $cache[$key] = (new ModuloRolModel())->getPermiso($idRol, $modulo);
        }

        return !empty($cache[$key]['puede_' . $accion]);
    }
}

if (!function_exists('es_admin')) {
    function es_admin(): bool
    {
        return (bool) session()->get('es_admin');
    }
}
