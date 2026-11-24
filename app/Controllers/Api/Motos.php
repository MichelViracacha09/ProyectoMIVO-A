<?php

namespace App\Controllers\Api;

use App\Models\MotoModel;

/**
 * Lectura de motos. Administrador: todas. Cliente: solo las suyas
 * (mismas reglas que el CRUD web en App\Controllers\Motos).
 */
class Motos extends ApiController
{
    public function index()
    {
        $motoModel = new MotoModel();

        $motos = $this->esAdmin()
            ? $motoModel->getAllWithCliente()
            : $motoModel->getByCliente($this->idCliente());

        return $this->respond(['data' => $motos]);
    }

    public function show($id = null)
    {
        $motoModel = new MotoModel();

        $moto = $this->esAdmin()
            ? $motoModel->find((int) $id)
            : $motoModel->getMotoForCliente((int) $id, $this->idCliente());

        if (!$moto) {
            return $this->failNotFound('Moto no encontrada.');
        }

        return $this->respond(['data' => $moto]);
    }
}
