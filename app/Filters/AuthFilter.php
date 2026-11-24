<?php
namespace App\Filters;

use App\Models\ClienteModel;
use App\Models\UsuarioModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Acceso denegado. Inicie sesión.');
        }

        // Si la cuenta se desactivó después de iniciar sesión, se cierra la sesión.
        $usuario = (new UsuarioModel())->find((int) session()->get('user_id'));
        $activo  = $usuario && strtoupper($usuario['estado']) === 'ACTIVO';

        if ($activo && session()->get('id_cliente')) {
            $cliente = (new ClienteModel())->find((int) session()->get('id_cliente'));
            $activo  = $cliente && (int) $cliente['id_estado_cliente'] === ClienteModel::ESTADO_ACTIVO;
        }

        if (!$activo) {
            session()->destroy();
            return redirect()->to('/login')->with('error', 'Su cuenta ya no está activa.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No se requiere posprocesamiento
    }
}
