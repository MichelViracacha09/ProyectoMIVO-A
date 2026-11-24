<?php

namespace App\Controllers\Api;

use App\Models\CitaModel;

/**
 * Lectura de citas. Administrador: todas. Cliente: solo las suyas
 * (mismas reglas que el CRUD web en App\Controllers\Citas).
 */
class Citas extends ApiController
{
    public function index()
    {
        $citaModel = new CitaModel();

        $citas = $this->esAdmin()
            ? $citaModel->getAllWithDetalle()
            : $citaModel->getByCliente($this->idCliente());

        return $this->respond(['data' => $citas]);
    }

    public function show($id = null)
    {
        $citaModel = new CitaModel();

        $cita = $this->esAdmin()
            ? $citaModel->find((int) $id)
            : $citaModel->getCitaForCliente((int) $id, $this->idCliente());

        // 404 también cuando la cita es de otro cliente, para no revelar su existencia
        if (!$cita) {
            return $this->failNotFound('Cita no encontrada.');
        }

        return $this->respond(['data' => $cita]);
    }
}
