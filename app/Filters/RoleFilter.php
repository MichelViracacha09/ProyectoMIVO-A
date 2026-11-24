<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Autorización por rol en el servidor.
 * Uso en rutas: ['filter' => 'role:admin'] o 'role:admin,tecnico'
 * Roles (tabla rol): 1 = admin, 2 = tecnico, 3 = cliente.
 */
class RoleFilter implements FilterInterface
{
    public const ROLES = ['admin' => 1, 'tecnico' => 2, 'cliente' => 3];

    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Acceso denegado. Inicie sesión.');
        }

        $permitidos = [];
        foreach ((array) $arguments as $arg) {
            if (isset(self::ROLES[$arg])) {
                $permitidos[] = self::ROLES[$arg];
            }
        }

        if (!in_array((int) session()->get('id_rol_num'), $permitidos, true)) {
            return redirect()->to('/home')->with('error', 'No tiene permisos para realizar esta acción.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
