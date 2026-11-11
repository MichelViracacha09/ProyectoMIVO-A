<?php

namespace App\Controllers;

use App\Models\ServicioModel;

class Servicios extends BaseController
{
    public function index()
    {
        $servicioModel = new ServicioModel();
        $data['servicios'] = $servicioModel->getAllWithTipo();

        return view('servicios/index', $data);
    }

    public function create()
    {
        return view('servicios/create');
    }

    public function store()
    {
        $rules = [
            'nombre_servicio'  => 'required|min_length[3]',
            'precio_servicio'  => 'required|numeric',
            'id_tipo_servicio' => 'required|integer|is_not_unique[tipo_servicio.id_tipo_servicio]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $servicioModel = new ServicioModel();

        $servicioModel->insert([
            'nombre_servicio'      => $this->request->getPost('nombre_servicio'),
            'descripcion_servicio' => $this->request->getPost('descripcion_servicio'),
            'precio_servicio'      => $this->request->getPost('precio_servicio'),
            'duracion_estimada'    => $this->request->getPost('duracion_estimada'),
            'garantia_servicio'    => $this->request->getPost('garantia_servicio'),
            'nivel_complejidad'    => $this->request->getPost('nivel_complejidad'),
            'id_tipo_servicio'     => $this->request->getPost('id_tipo_servicio'),
        ]);

        return redirect()->to('/servicios')->with('success', 'Servicio creado con éxito.');
    }

    public function edit($id)
    {
        $servicioModel = new ServicioModel();
        $servicio = $servicioModel->find($id);

        if (!$servicio) {
            return redirect()->to('/servicios')->with('error', 'Servicio no encontrado.');
        }

        $data['servicio'] = $servicio;

        return view('servicios/edit', $data);
    }

    public function update($id)
    {
        $servicioModel = new ServicioModel();
        $servicio = $servicioModel->find($id);

        if (!$servicio) {
            return redirect()->to('/servicios')->with('error', 'Servicio no encontrado.');
        }

        $rules = [
            'nombre_servicio' => 'required|min_length[3]',
            'precio_servicio' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $servicioModel->update($id, [
            'nombre_servicio'      => $this->request->getPost('nombre_servicio'),
            'descripcion_servicio' => $this->request->getPost('descripcion_servicio'),
            'precio_servicio'      => $this->request->getPost('precio_servicio'),
            'duracion_estimada'    => $this->request->getPost('duracion_estimada'),
            'garantia_servicio'    => $this->request->getPost('garantia_servicio'),
            'nivel_complejidad'    => $this->request->getPost('nivel_complejidad'),
        ]);

        return redirect()->to('/servicios')->with('success', 'Servicio actualizado con éxito.');
    }

    public function delete($id)
    {
        $servicioModel = new ServicioModel();
        $servicio = $servicioModel->find($id);

        if (!$servicio) {
            return redirect()->to('/servicios')->with('error', 'Servicio no encontrado.');
        }

        $servicioModel->delete($id);

        return redirect()->to('/servicios')->with('success', 'Servicio eliminado con éxito.');
    }
}