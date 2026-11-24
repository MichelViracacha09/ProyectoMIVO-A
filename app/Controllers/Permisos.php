<?php

namespace App\Controllers;

use App\Filters\RoleFilter;
use App\Models\ModuloRolModel;
use App\Models\RolModel;

/**
 * Asignación de módulos y permisos (ver/crear/editar/eliminar) por rol sobre la tabla modulo_rol.
 * Solo administrador (filtro 'role:admin' en Config/Routes.php). Los permisos del rol
 * Administrador no se editan para evitar bloquear el acceso al sistema.
 */
class Permisos extends BaseController
{
    public function index()
    {
        $rolModel = new RolModel();
        $roles    = $rolModel->orderBy('id_rol', 'ASC')->findAll();

        $idRol = (int) $this->request->getGet('id_rol');
        $rol   = $idRol > 0 ? $rolModel->find($idRol) : null;
        if (!$rol) {
            $rol = $roles[0] ?? null;
        }
        if (!$rol) {
            return redirect()->to('/home')->with('error', 'No hay roles registrados.');
        }

        return view('permisos/index', [
            'titulo'   => 'Permisos',
            'roles'    => $roles,
            'rol'      => $rol,
            'matriz'   => (new ModuloRolModel())->getMatrizByRol((int) $rol['id_rol']),
            'acciones' => ModuloRolModel::ACCIONES,
            'bloqueado' => (int) $rol['id_rol'] === RoleFilter::ROLES['admin'],
        ]);
    }

    public function save($id)
    {
        $id  = (int) $id;
        $rol = (new RolModel())->find($id);
        if (!$rol) {
            return redirect()->to('/permisos')->with('error', 'Rol no encontrado.');
        }
        if ($id === RoleFilter::ROLES['admin']) {
            return redirect()->to('/permisos?id_rol=' . $id)->with('error', 'Los permisos del rol Administrador no se pueden modificar.');
        }

        $modulosValidos = array_column((new ModuloRolModel())->getMatrizByRol($id), 'id_modulo');
        $enviado        = (array) $this->request->getPost('permiso');

        $matriz = [];
        foreach ($modulosValidos as $idModulo) {
            $fila = (array) ($enviado[$idModulo] ?? []);
            foreach (ModuloRolModel::ACCIONES as $accion) {
                $matriz[(int) $idModulo]['puede_' . $accion] = !empty($fila[$accion]) ? 1 : 0;
            }
            // Crear, editar o eliminar sin poder ver no tiene sentido: se exige "ver"
            if (array_sum($matriz[(int) $idModulo]) > 0) {
                $matriz[(int) $idModulo]['puede_ver'] = 1;
            }
        }

        if (!(new ModuloRolModel())->guardarMatriz($id, $matriz)) {
            return redirect()->back()->with('error', 'No se pudieron guardar los permisos.');
        }

        return redirect()->to('/permisos?id_rol=' . $id)->with('success', 'Permisos actualizados.');
    }
}
