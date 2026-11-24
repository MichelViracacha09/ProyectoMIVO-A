<?php

namespace App\Controllers;

use App\Filters\RoleFilter;
use App\Models\RolModel;
use App\Models\UsuarioModel;

/**
 * Gestión de roles (solo administrador, filtro 'role:admin' en Config/Routes.php).
 * La tabla rol no tiene campo de estado: un rol con usuarios no se elimina y los roles base
 * (Administrador, Técnico, Cliente) están protegidos.
 */
class Roles extends BaseController
{
    private function esRolBase(int $idRol): bool
    {
        return in_array($idRol, RoleFilter::ROLES, true);
    }

    private function reglas(?int $id = null): array
    {
        $ignorar = $id !== null ? ',id_rol,' . $id : '';

        return [
            'nombre'      => 'required|min_length[3]|max_length[50]|is_unique[rol.nombre' . $ignorar . ']',
            'descripcion' => 'permit_empty|max_length[255]',
        ];
    }

    public function index()
    {
        return view('roles/index', [
            'titulo' => 'Roles',
            'roles'  => (new RolModel())->getAllWithUsuarios(),
            'base'   => RoleFilter::ROLES,
        ]);
    }

    public function show($id)
    {
        $rol = (new RolModel())->find((int) $id);
        if (!$rol) {
            return redirect()->to('/roles')->with('error', 'Rol no encontrado.');
        }

        return view('roles/show', [
            'titulo'   => 'Detalle del rol',
            'rol'      => $rol,
            'usuarios' => (new UsuarioModel())->filtrar(['id_rol' => (int) $id])->findAll(),
        ]);
    }

    public function create()
    {
        return view('roles/form', ['titulo' => 'Nuevo rol']);
    }

    public function store()
    {
        if (!$this->validate($this->reglas())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        (new RolModel())->insert([
            'nombre'      => trim((string) $this->request->getPost('nombre')),
            'descripcion' => trim((string) $this->request->getPost('descripcion')) ?: null,
        ]);

        return redirect()->to('/roles')->with('success', 'Rol creado. Asígnele módulos en Permisos.');
    }

    public function edit($id)
    {
        $rol = (new RolModel())->find((int) $id);
        if (!$rol) {
            return redirect()->to('/roles')->with('error', 'Rol no encontrado.');
        }

        return view('roles/form', ['titulo' => 'Editar rol', 'rol' => $rol]);
    }

    public function update($id)
    {
        $id  = (int) $id;
        $rol = (new RolModel())->find($id);
        if (!$rol) {
            return redirect()->to('/roles')->with('error', 'Rol no encontrado.');
        }

        // Los roles base conservan su nombre porque la aplicación depende de ellos
        $reglas = $this->esRolBase($id) ? ['descripcion' => 'permit_empty|max_length[255]'] : $this->reglas($id);
        if (!$this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $datos = ['descripcion' => trim((string) $this->request->getPost('descripcion')) ?: null];
        if (!$this->esRolBase($id)) {
            $datos['nombre'] = trim((string) $this->request->getPost('nombre'));
        }

        (new RolModel())->update($id, $datos);

        return redirect()->to('/roles')->with('success', 'Rol actualizado con éxito.');
    }

    public function delete($id)
    {
        $id       = (int) $id;
        $rolModel = new RolModel();
        if (!$rolModel->find($id)) {
            return redirect()->to('/roles')->with('error', 'Rol no encontrado.');
        }

        if ($this->esRolBase($id)) {
            return redirect()->to('/roles')->with('error', 'Los roles base del sistema no se pueden eliminar.');
        }
        if ($rolModel->contarUsuarios($id) > 0) {
            return redirect()->to('/roles')->with('error', 'El rol tiene usuarios asociados y no se puede eliminar.');
        }

        $rolModel->delete($id);

        return redirect()->to('/roles')->with('success', 'Rol eliminado con éxito.');
    }
}
