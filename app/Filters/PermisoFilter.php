<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Autoriza en el servidor según modulo_rol.
 * Uso en rutas: ['filter' => 'permiso:motos,crear']
 */
class PermisoFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Acceso denegado. Inicie sesión.');
        }

        helper('permisos');
        [$modulo, $accion] = array_pad((array) $arguments, 2, 'ver');

        if (!puede((string) $modulo, (string) $accion)) {
            return redirect()->to('/home')->with('error', 'No tiene permisos para realizar esta acción.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
