<?php

namespace App\Controllers;

use App\Models\CitaModel;
use App\Models\ServicioModel;

class Citas extends BaseController
{
    public function index()
    {
        $citaModel = new CitaModel();
        $esAdmin   = (bool) session()->get('es_admin');

        if ($esAdmin) {
            $data['citas'] = $citaModel->getAllWithDetalle();
        } else {
            $idCliente = (int) session()->get('id_cliente');
            $data['citas'] = $citaModel->getByCliente($idCliente);
        }

        $data['esAdmin'] = $esAdmin;

        return view('citas/index', $data);
    }

    public function create()
    {
        $servicioModel = new ServicioModel();
        $data['servicios'] = $servicioModel->findAll();

        return view('citas/create', $data);
    }

    public function store()
    {
        $rules = [
            'fecha_cita'  => 'required|valid_date[Y-m-d]',
            'hora_cita'   => 'required',
            'id_servicio' => 'required|integer|is_not_unique[servicio.id_servicio]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $citaModel = new CitaModel();
        $idCliente = (int) session()->get('id_cliente');

        $citaModel->insert([
            'fecha_cita'           => $this->request->getPost('fecha_cita'),
            'hora_cita'            => $this->request->getPost('hora_cita'),
            'servicio_a_realizar'  => $this->request->getPost('observaciones'),
            'estado_cita'          => 'PENDIENTE',
            'id_cliente'           => $idCliente,
            'id_servicio'          => $this->request->getPost('id_servicio'),
        ]);

        return redirect()->to('/citas')->with('success', 'Cita agendada con éxito.');
    }

    public function edit($id)
    {
        $citaModel = new CitaModel();
        $esAdmin   = (bool) session()->get('es_admin');

        if ($esAdmin) {
            $cita = $citaModel->find($id);
        } else {
            $idCliente = (int) session()->get('id_cliente');
            $cita = $citaModel->getCitaForCliente((int) $id, $idCliente);
        }

        if (!$cita) {
            return redirect()->to('/citas')->with('error', 'Cita no encontrada.');
        }

        $data['cita']    = $cita;
        $data['esAdmin'] = $esAdmin;

        return view('citas/edit', $data);
    }

    public function update($id)
    {
        $citaModel = new CitaModel();
        $esAdmin   = (bool) session()->get('es_admin');

        if ($esAdmin) {
            $cita = $citaModel->find($id);
        } else {
            $idCliente = (int) session()->get('id_cliente');
            $cita = $citaModel->getCitaForCliente((int) $id, $idCliente);
        }

        if (!$cita) {
            return redirect()->to('/citas')->with('error', 'Cita no encontrada.');
        }

        $rules = [
            'fecha_cita' => 'required|valid_date[Y-m-d]',
            'hora_cita'  => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'fecha_cita' => $this->request->getPost('fecha_cita'),
            'hora_cita'  => $this->request->getPost('hora_cita'),
        ];

        // Solo el administrador puede cambiar el estado de la cita
        if ($esAdmin) {
            $updateData['estado_cita'] = $this->request->getPost('estado_cita');
        }

        $citaModel->update($id, $updateData);

        return redirect()->to('/citas')->with('success', 'Cita actualizada con éxito.');
    }

    public function delete($id)
    {
        $citaModel = new CitaModel();
        $esAdmin   = (bool) session()->get('es_admin');

        if ($esAdmin) {
            $cita = $citaModel->find($id);
        } else {
            $idCliente = (int) session()->get('id_cliente');
            $cita = $citaModel->getCitaForCliente((int) $id, $idCliente);
        }

        if (!$cita) {
            return redirect()->to('/citas')->with('error', 'Cita no encontrada.');
        }

        $citaModel->delete($id);

        return redirect()->to('/citas')->with('success', 'Cita eliminada con éxito.');
    }
}