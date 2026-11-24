<?php

namespace App\Controllers\Api;

use App\Models\ClienteModel;

/**
 * Lectura de clientes. Administrador: listado y detalle.
 * Cliente: solo su propio registro.
 */
class Clientes extends ApiController
{
    private const CAMPOS = 'id_cliente, nombre_completo, numero_identificacion, numero_celular, '
        . 'correo_electronico, direccion, fecha_nacimiento, id_estado_cliente, id_identificacion';

    public function index()
    {
        if (!$this->esAdmin()) {
            return $this->failForbidden('No tiene permisos para listar clientes.');
        }

        $clientes = (new ClienteModel())->select(self::CAMPOS)->orderBy('id_cliente', 'ASC')->findAll();

        return $this->respond(['data' => $clientes]);
    }

    public function show($id = null)
    {
        if (!$this->esAdmin() && (int) $id !== $this->idCliente()) {
            return $this->failNotFound('Cliente no encontrado.');
        }

        $cliente = (new ClienteModel())->select(self::CAMPOS)->find((int) $id);

        if (!$cliente) {
            return $this->failNotFound('Cliente no encontrado.');
        }

        return $this->respond(['data' => $cliente]);
    }
}
