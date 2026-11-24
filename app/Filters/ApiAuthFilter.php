<?php

namespace App\Filters;

use App\Libraries\ApiToken;
use App\Models\ClienteModel;
use App\Models\UsuarioModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

/**
 * Protege /api/*: exige "Authorization: Bearer <token>" válido y que
 * el usuario (y el cliente, si aplica) sigan activos en la base de datos.
 */
class ApiAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');
        if (!preg_match('/^Bearer\s+(\S+)$/', $header, $m)) {
            return $this->deny(401, 'Token requerido.');
        }

        $claims = ApiToken::decode($m[1]);
        if ($claims === null || empty($claims['user_id'])) {
            return $this->deny(401, 'Token inválido o expirado.');
        }

        $user = (new UsuarioModel())->find((int) $claims['user_id']);
        if (!$user || strtoupper($user['estado']) !== 'ACTIVO' || (int) $user['id_rol'] !== (int) $claims['id_rol']) {
            return $this->deny(401, 'Usuario no autorizado.');
        }

        if (!empty($claims['id_cliente'])) {
            $cliente = (new ClienteModel())->find((int) $claims['id_cliente']);
            if (!$cliente || (int) $cliente['id_usuario'] !== (int) $user['id_usuario'] || (int) $cliente['id_estado_cliente'] !== 1) {
                return $this->deny(401, 'Cuenta de cliente no activa.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    private function deny(int $status, string $message): ResponseInterface
    {
        return Services::response()->setStatusCode($status)->setJSON(['error' => $message]);
    }
}
