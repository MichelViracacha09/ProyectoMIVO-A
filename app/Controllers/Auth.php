<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\ClienteModel;

class Auth extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/home');
        }

        return view('auth/login');
    }

    public function processLogin()
    {
        $session = session();
        $usuarioModel = new UsuarioModel();

        $correo   = trim($this->request->getPost('correo'));
        $password = trim($this->request->getPost('password'));

        if (empty($correo) || empty($password)) {
            return redirect()->back()->with('error', 'Debe ingresar ambos campos.')->withInput();
        }

        $user = $usuarioModel->getUserByEmail($correo);

        if ($user) {
            if (password_verify($password, $user['hash'])) {

                if (strtoupper($user['estado']) !== 'ACTIVO') {
                    return redirect()->back()->with('error', 'Su cuenta se encuentra: ' . $user['estado']);
                }

                // Cliente inactivo (2) o bloqueado (3) según estado_cliente no puede ingresar
                if (!empty($user['id_cliente']) && (int) $user['id_estado_cliente'] !== 1) {
                    return redirect()->back()->with('error', 'Su cuenta de cliente no está activa. Contacte al taller.');
                }

                // Detectar si es administrador o cliente
                $esAdmin = !empty($user['id_administrador']);

                $sessionData = [
                    'user_id'    => (int) $user['id_usuario'],
                    'id_rol_num' => (int) $user['id_rol'],
                    'rol'        => $user['rol_nombre'],
                    'logged_in'  => true,
                    'es_admin'   => $esAdmin,
                    'nombre'     => $esAdmin ? $user['admin_nombre'] : $user['cliente_nombre'],
                    'correo'     => $esAdmin ? $user['admin_correo'] : $user['cliente_correo'],

                ];

                if ($esAdmin) {
                    $sessionData['id_administrador'] = (int) $user['id_administrador'];
                } else {
                    $sessionData['id_cliente'] = (int) $user['id_cliente'];
                }

                // Evita la fijación de sesión: nuevo id de sesión tras autenticarse
                $session->regenerate(true);
                $session->set($sessionData);

                if ($esAdmin) {
                    (new \App\Models\AdministradorModel())->update((int) $user['id_administrador'], ['ultimo_acceso' => date('Y-m-d H:i:s')]);
                }

                return redirect()->to('/home')->with('success', 'Bienvenido al sistema.');
            }
        }

        return redirect()->back()->with('error', 'Credenciales inválidas. Intente nuevamente.');
    }

    public function logout()
    {
        
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Sesión cerrada correctamente.');
    }
        public function register()
    {
        return view('auth/register');
    }

    public function processRegister()
    {
        $rules = [
            'nombre_completo'       => 'required|min_length[3]',
            'numero_identificacion' => 'required|is_unique[cliente.numero_identificacion]',
            'correo_electronico'    => 'required|valid_email|is_unique[cliente.correo_electronico]',
            'numero_celular'        => 'required|regex_match[/^\+?[0-9]{7,15}$/]',
            'id_identificacion'     => 'required|integer|is_not_unique[tipo_identificacion.id_identificacion]',
            'password'              => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();

        // El correo también es el usuario de acceso: no puede coincidir con el de un administrador
        if ($db->table('administrador')->where('correo', $this->request->getPost('correo_electronico'))->countAllResults() > 0) {
            return redirect()->back()->withInput()->with('errors', ['correo_electronico' => 'Ese correo ya está registrado.']);
        }
        $db->transStart();

        $usuarioModel = new UsuarioModel();
        $clienteModel = new ClienteModel();

        // 1. Crear el usuario base (rol 3 = Cliente)
        $userId = $usuarioModel->insert([
            'hash'   => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'estado' => 'ACTIVO',
            'id_rol' => 3
        ], true);

        // 2. Crear el registro de cliente asociado
        $clienteModel->insert([
            'nombre_completo'       => $this->request->getPost('nombre_completo'),
            'numero_identificacion' => $this->request->getPost('numero_identificacion'),
            'numero_celular'        => $this->request->getPost('numero_celular'),
            'correo_electronico'    => $this->request->getPost('correo_electronico'),
            'direccion'             => $this->request->getPost('direccion'),
            'id_estado_cliente'     => 1, // Activo
            'id_identificacion'     => $this->request->getPost('id_identificacion'),
            'id_usuario'            => $userId
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'No se pudo completar el registro.');
        }

        return redirect()->to('/login')->with('success', 'Cuenta creada con éxito. Inicie sesión.');
    }
}
