<?php

namespace App\Controllers;

use App\Models\MotoModel;
use App\Models\ClienteModel;

class Motos extends BaseController
{
    public function index()
    {
        $motoModel = new MotoModel();
        $esAdmin   = (bool) session()->get('es_admin');

        if ($esAdmin) {
            $data['motos'] = $motoModel->getAllWithCliente();
        } else {
            $idCliente = (int) session()->get('id_cliente');
            $data['motos'] = $motoModel->getByCliente($idCliente);
        }

        $data['esAdmin'] = $esAdmin;

        return view('motos/index', $data);
    }

    public function create()
    {
        $data['esAdmin'] = (bool) session()->get('es_admin');

        // Si es admin, necesita elegir a qué cliente pertenece la moto
        if ($data['esAdmin']) {
            $clienteModel = new ClienteModel();
            $data['clientes'] = $clienteModel->findAll();
        }

        return view('motos/create', $data);
    }

    public function store()
    {
        $rules = [
            'placa' => 'required|min_length[5]|max_length[15]|is_unique[moto.placa]',
            'linea' => 'required',
        ];

        $esAdmin = (bool) session()->get('es_admin');

        if ($esAdmin) {
            $rules['id_cliente'] = 'required|integer|is_not_unique[cliente.id_cliente]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $motoModel = new MotoModel();

        $idCliente = $esAdmin
            ? (int) $this->request->getPost('id_cliente')
            : (int) session()->get('id_cliente');

        $motoModel->insert([
            'placa'       => strtoupper($this->request->getPost('placa')),
            'linea'       => $this->request->getPost('linea'),
            'modelo'      => $this->request->getPost('modelo'),
            'color'       => $this->request->getPost('color'),
            'cilindraje'  => $this->request->getPost('cilindraje'),
            'kilometraje' => $this->request->getPost('kilometraje'),
            'id_cliente'  => $idCliente
        ]);

        return redirect()->to('/motos')->with('success', 'Moto registrada con éxito.');
    }

    public function edit($id)
    {
        $motoModel = new MotoModel();
        $esAdmin   = (bool) session()->get('es_admin');

        if ($esAdmin) {
            $moto = $motoModel->find($id);
        } else {
            $idCliente = (int) session()->get('id_cliente');
            $moto = $motoModel->getMotoForCliente((int) $id, $idCliente);
        }

        if (!$moto) {
            return redirect()->to('/motos')->with('error', 'Moto no encontrada.');
        }

        $data['moto'] = $moto;

        return view('motos/edit', $data);
    }

    public function update($id)
    {
        $motoModel = new MotoModel();
        $esAdmin   = (bool) session()->get('es_admin');

        if ($esAdmin) {
            $moto = $motoModel->find($id);
        } else {
            $idCliente = (int) session()->get('id_cliente');
            $moto = $motoModel->getMotoForCliente((int) $id, $idCliente);
        }

        if (!$moto) {
            return redirect()->to('/motos')->with('error', 'Moto no encontrada.');
        }

        $rules = [
            'linea' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $motoModel->update($id, [
            'linea'       => $this->request->getPost('linea'),
            'modelo'      => $this->request->getPost('modelo'),
            'color'       => $this->request->getPost('color'),
            'cilindraje'  => $this->request->getPost('cilindraje'),
            'kilometraje' => $this->request->getPost('kilometraje'),
        ]);

        return redirect()->to('/motos')->with('success', 'Moto actualizada con éxito.');
    }

    public function delete($id)
    {
        $motoModel = new MotoModel();
        $esAdmin   = (bool) session()->get('es_admin');

        if ($esAdmin) {
            $moto = $motoModel->find($id);
        } else {
            $idCliente = (int) session()->get('id_cliente');
            $moto = $motoModel->getMotoForCliente((int) $id, $idCliente);
        }

        if (!$moto) {
            return redirect()->to('/motos')->with('error', 'Moto no encontrada.');
        }

        $motoModel->delete($id);

        return redirect()->to('/motos')->with('success', 'Moto eliminada con éxito.');
    }
}