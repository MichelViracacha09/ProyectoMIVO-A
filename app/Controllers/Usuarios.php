<?php

namespace App\Controllers;

use App\Filters\RoleFilter;
use App\Models\AdministradorModel;
use App\Models\RolModel;
use App\Models\UsuarioModel;

/**
 * Gestión de usuarios internos (solo administrador, filtro 'role:admin' en Config/Routes.php).
 *
 * - Crea usuarios con perfil de Administrador. Los clientes se gestionan en el módulo Clientes
 *   (o con el registro público) y los técnicos pertenecen al módulo Node.js, por lo que no se crean aquí.
 * - El rol de un usuario lo determina su perfil; por eso no se cambia desde la edición.
 */
class Usuarios extends BaseController
{
    private function reglasPerfilAdmin(?int $idAdmin = null): array
    {
        $ignorar = $idAdmin !== null ? ',id_administrador,' . $idAdmin : '';

        return [
            'nombre_completo' => 'required|min_length[3]|max_length[150]',
            'usuario_login'   => 'required|alpha_dash|min_length[4]|max_length[50]|is_unique[administrador.usuario_login' . $ignorar . ']',
            'correo'          => 'required|valid_email|max_length[150]|is_unique[administrador.correo' . $ignorar . ']',
        ];
    }

    /** El correo también sirve para iniciar sesión: no puede coincidir con el de un cliente. */
    private function correoLibreDeCliente(string $correo): bool
    {
        return \Config\Database::connect()->table('cliente')->where('correo_electronico', $correo)->countAllResults() === 0;
    }

    public function index()
    {
        $model   = new UsuarioModel();
        $filtros = [
            'q'      => trim((string) $this->request->getGet('q')),
            'id_rol' => $this->request->getGet('id_rol'),
            'estado' => $this->request->getGet('estado'),
        ];

        return view('usuarios/index', [
            'titulo'   => 'Usuarios',
            'usuarios' => $model->filtrar($filtros)->paginate(10),
            'pager'    => $model->pager,
            'filtros'  => $filtros,
            'roles'    => (new RolModel())->findAll(),
            'estados'  => UsuarioModel::ESTADOS,
        ]);
    }

    public function show($id)
    {
        $usuario = (new UsuarioModel())->getDetalle((int) $id);
        if (!$usuario) {
            return redirect()->to('/usuarios')->with('error', 'Usuario no encontrado.');
        }

        return view('usuarios/show', ['titulo' => 'Detalle del usuario', 'usuario' => $usuario]);
    }

    public function create()
    {
        return view('usuarios/create', ['titulo' => 'Nuevo usuario administrador']);
    }

    public function store()
    {
        $rules = $this->reglasPerfilAdmin() + [
            'password' => 'required|min_length[8]|max_length[72]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $correo = strtolower(trim((string) $this->request->getPost('correo')));
        if (!$this->correoLibreDeCliente($correo)) {
            return redirect()->back()->withInput()->with('errors', ['correo' => 'Ese correo pertenece a otro usuario del sistema.']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $idUsuario = (new UsuarioModel())->insert([
            'hash'   => password_hash((string) $this->request->getPost('password'), PASSWORD_BCRYPT),
            'estado' => 'ACTIVO',
            'id_rol' => RoleFilter::ROLES['admin'],
        ], true);

        (new AdministradorModel())->insert([
            'nombre_completo'    => trim((string) $this->request->getPost('nombre_completo')),
            'usuario_login'      => trim((string) $this->request->getPost('usuario_login')),
            'correo'             => $correo,
            'permisos_asignados' => 'total',
            'id_usuario'         => $idUsuario,
        ]);

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'No se pudo crear el usuario.');
        }

        return redirect()->to('/usuarios')->with('success', 'Usuario administrador creado con éxito.');
    }

    public function edit($id)
    {
        $usuario = (new UsuarioModel())->getDetalle((int) $id);
        if (!$usuario) {
            return redirect()->to('/usuarios')->with('error', 'Usuario no encontrado.');
        }

        return view('usuarios/edit', [
            'titulo'  => 'Editar usuario',
            'usuario' => $usuario,
            'estados' => UsuarioModel::ESTADOS,
            'esMismo' => (int) $usuario['id_usuario'] === (int) session()->get('user_id'),
        ]);
    }

    public function update($id)
    {
        $id           = (int) $id;
        $usuarioModel = new UsuarioModel();
        $usuario      = $usuarioModel->getDetalle($id);
        if (!$usuario) {
            return redirect()->to('/usuarios')->with('error', 'Usuario no encontrado.');
        }

        $esAdminPerfil = !empty($usuario['id_administrador']);
        $rules = [
            'estado'   => 'required|in_list[' . implode(',', UsuarioModel::ESTADOS) . ']',
            'password' => 'permit_empty|min_length[8]|max_length[72]',
        ];
        if ($esAdminPerfil) {
            $rules += $this->reglasPerfilAdmin((int) $usuario['id_administrador']);
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $estado = (string) $this->request->getPost('estado');

        // Un administrador no puede desactivarse a sí mismo ni dejar el sistema sin administradores activos
        if ($estado !== 'ACTIVO') {
            if ($id === (int) session()->get('user_id')) {
                return redirect()->back()->withInput()->with('error', 'No puede desactivar su propia cuenta.');
            }
            if ($esAdminPerfil && strtoupper($usuario['estado']) === 'ACTIVO' && $usuarioModel->contarAdministradoresActivos() <= 1) {
                return redirect()->back()->withInput()->with('error', 'Debe existir al menos un administrador activo.');
            }
        }

        $correo = null;
        if ($esAdminPerfil) {
            $correo = strtolower(trim((string) $this->request->getPost('correo')));
            if (!$this->correoLibreDeCliente($correo)) {
                return redirect()->back()->withInput()->with('errors', ['correo' => 'Ese correo pertenece a otro usuario del sistema.']);
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $datosUsuario = ['estado' => $estado];
        $password     = (string) $this->request->getPost('password');
        if ($password !== '') {
            $datosUsuario['hash'] = password_hash($password, PASSWORD_BCRYPT);
        }
        $usuarioModel->update($id, $datosUsuario);

        if ($esAdminPerfil) {
            (new AdministradorModel())->update((int) $usuario['id_administrador'], [
                'nombre_completo' => trim((string) $this->request->getPost('nombre_completo')),
                'usuario_login'   => trim((string) $this->request->getPost('usuario_login')),
                'correo'          => $correo,
            ]);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'No se pudo actualizar el usuario.');
        }

        return redirect()->to('/usuarios')->with('success', 'Usuario actualizado con éxito.');
    }
}
