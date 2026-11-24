<?php

namespace App\Controllers;

use App\Models\ServicioModel;
use App\Models\TipoServicioModel;

/**
 * Catálogo de servicios. Permisos validados por los filtros 'permiso:servicios,<accion>'
 * (Config/Routes.php). La tabla servicio no tiene campo de estado, por lo que un servicio
 * en uso (citas o motos) no se elimina.
 */
class Servicios extends BaseController
{
    private function reglas(): array
    {
        return [
            'nombre_servicio'      => 'required|min_length[3]|max_length[150]',
            'descripcion_servicio' => 'permit_empty|max_length[255]',
            'precio_servicio'      => 'required|decimal|greater_than_equal_to[0]|less_than[10000000000]',
            'duracion_estimada'    => 'permit_empty|max_length[50]',
            'garantia_servicio'    => 'permit_empty|max_length[100]',
            'nivel_complejidad'    => 'permit_empty|in_list[Bajo,Medio,Alto]',
        ];
    }

    private function datosForm(): array
    {
        return [
            'nombre_servicio'      => trim((string) $this->request->getPost('nombre_servicio')),
            'descripcion_servicio' => trim((string) $this->request->getPost('descripcion_servicio')) ?: null,
            'precio_servicio'      => $this->request->getPost('precio_servicio'),
            'duracion_estimada'    => trim((string) $this->request->getPost('duracion_estimada')) ?: null,
            'garantia_servicio'    => trim((string) $this->request->getPost('garantia_servicio')) ?: null,
            'nivel_complejidad'    => $this->request->getPost('nivel_complejidad') ?: null,
        ];
    }

    public function index()
    {
        $model   = new ServicioModel();
        $filtros = [
            'q'                => trim((string) $this->request->getGet('q')),
            'id_tipo_servicio' => $this->request->getGet('id_tipo_servicio'),
        ];

        return view('servicios/index', [
            'titulo'    => 'Servicios',
            'servicios' => $model->filtrar($filtros)->paginate(10),
            'pager'     => $model->pager,
            'filtros'   => $filtros,
            'tipos'     => (new TipoServicioModel())->findAll(),
        ]);
    }

    public function show($id)
    {
        $servicio = (new ServicioModel())->getDetalle((int) $id);
        if (!$servicio) {
            return redirect()->to('/servicios')->with('error', 'Servicio no encontrado.');
        }

        return view('servicios/show', [
            'titulo'   => 'Detalle del servicio',
            'servicio' => $servicio,
            'usos'     => (new ServicioModel())->contarUsos((int) $id),
        ]);
    }

    public function create()
    {
        return view('servicios/create', ['tipos' => (new TipoServicioModel())->findAll()]);
    }

    public function store()
    {
        $rules = $this->reglas() + [
            'id_tipo_servicio' => 'required|integer|is_not_unique[tipo_servicio.id_tipo_servicio]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $servicioModel = new ServicioModel();
        $datos         = $this->datosForm() + ['id_tipo_servicio' => (int) $this->request->getPost('id_tipo_servicio')];

        if ($servicioModel->existeDuplicado($datos['nombre_servicio'], $datos['id_tipo_servicio'])) {
            return redirect()->back()->withInput()->with('errors', ['nombre_servicio' => 'Ya existe un servicio con ese nombre y tipo.']);
        }

        $servicioModel->insert($datos);

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
        $id            = (int) $id;
        $servicioModel = new ServicioModel();
        $servicio      = $servicioModel->find($id);
        if (!$servicio) {
            return redirect()->to('/servicios')->with('error', 'Servicio no encontrado.');
        }

        if (!$this->validate($this->reglas())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $datos = $this->datosForm();
        if ($servicioModel->existeDuplicado($datos['nombre_servicio'], (int) $servicio['id_tipo_servicio'], $id)) {
            return redirect()->back()->withInput()->with('errors', ['nombre_servicio' => 'Ya existe un servicio con ese nombre y tipo.']);
        }

        $servicioModel->update($id, $datos);

        return redirect()->to('/servicios')->with('success', 'Servicio actualizado con éxito.');
    }

    public function delete($id)
    {
        $servicioModel = new ServicioModel();
        $servicio = $servicioModel->find((int) $id);
        if (!$servicio) {
            return redirect()->to('/servicios')->with('error', 'Servicio no encontrado.');
        }

        if ($servicioModel->contarUsos((int) $id) > 0) {
            return redirect()->to('/servicios')->with('error', 'El servicio está asociado a citas o motos y no se puede eliminar.');
        }

        $servicioModel->delete((int) $id);
        return redirect()->to('/servicios')->with('success', 'Servicio eliminado con éxito.');
    }
}
