<?php

namespace App\Controllers\Api;

use App\Libraries\ApiToken;
use App\Models\UsuarioModel;

class Auth extends ApiController
{
    /**
     * POST /api/login  {correo, password}  ->  {token, expira_en, usuario}
     * Usa las mismas credenciales y reglas de estado que el login web.
     */
    public function login()
    {
        $data     = $this->request->getJSON(true) ?? $this->request->getPost();
        $correo   = trim((string) ($data['correo'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        if ($correo === '' || $password === '') {
            return $this->failValidationErrors('Debe ingresar correo y password.');
        }

        $user = (new UsuarioModel())->getUserByEmail($correo);

        if (!$user || !password_verify($password, $user['hash'])) {
            return $this->failUnauthorized('Credenciales inválidas.');
        }
        if (strtoupper($user['estado']) !== 'ACTIVO') {
            return $this->failForbidden('Cuenta no activa.');
        }
        if (!empty($user['id_cliente']) && (int) $user['id_estado_cliente'] !== 1) {
            return $this->failForbidden('Cuenta de cliente no activa.');
        }

        $esAdmin = !empty($user['id_administrador']);
        $claims  = [
            'user_id' => (int) $user['id_usuario'],
            'id_rol'  => (int) $user['id_rol'],
        ];
        if ($esAdmin) {
            $claims['id_administrador'] = (int) $user['id_administrador'];
        } else {
            $claims['id_cliente'] = (int) $user['id_cliente'];
        }

        return $this->respond([
            'token'     => ApiToken::encode($claims),
            'expira_en' => ApiToken::TTL,
            'usuario'   => [
                'id_usuario' => $claims['user_id'],
                'rol'        => $user['rol_nombre'],
                'nombre'     => $esAdmin ? $user['admin_nombre'] : $user['cliente_nombre'],
            ],
        ]);
    }
}
