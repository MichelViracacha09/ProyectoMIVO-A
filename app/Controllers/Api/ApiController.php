<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\ApiToken;
use CodeIgniter\API\ResponseTrait;

/**
 * Base de la API REST. El filtro 'apiAuth' ya validó el token;
 * aquí solo se leen los claims para autorizar por rol y propietario.
 */
abstract class ApiController extends BaseController
{
    use ResponseTrait;

    protected function claims(): array
    {
        preg_match('/^Bearer\s+(\S+)$/', $this->request->getHeaderLine('Authorization'), $m);
        return ApiToken::decode($m[1] ?? '') ?? [];
    }

    protected function esAdmin(): bool
    {
        return (int) ($this->claims()['id_rol'] ?? 0) === 1;
    }

    protected function idCliente(): int
    {
        return (int) ($this->claims()['id_cliente'] ?? 0);
    }
}
