<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\MotoModel;

/**
 * Gestión de motocicletas. Permisos validados por los filtros 'permiso:motos,<accion>'
 * (Config/Routes.php). Un cliente solo accede a sus propias motos.
 */
class Motos extends BaseController
{
    private function esAdmin(): bool
    {
        return (bool) session()->get('es_admin');
    }

    /** Id del cliente al que se restringe la consulta; null para el administrador. */
    private function idClienteScope(): ?int
    {
        return $this->esAdmin() ? null : (int) session()->get('id_cliente');
    }

    private function buscarMoto(int $id): ?array
    {
        $motoModel = new MotoModel();

        return $this->esAdmin()
            ? $motoModel->find($id)
            : $motoModel->getMotoForCliente($id, (int) session()->get('id_cliente'));
    }

    private function reglasComunes(): array
    {
        return [
            'linea'       => 'required|max_length[100]',
            'modelo'      => 'permit_empty|integer|greater_than_equal_to[1950]|less_than_equal_to[' . ((int) date('Y') + 1) . ']',
            'color'       => 'permit_empty|max_length[50]',
            'cilindraje'  => 'permit_empty|integer|greater_than[0]',
            'kilometraje' => 'permit_empty|integer|greater_than_equal_to[0]',
        ];
    }

    private function datosComunes(): array
    {
        $post = $this->request->getPost();

        return [
            'linea'       => trim((string) ($post['linea'] ?? '')),
            'modelo'      => ($post['modelo'] ?? '') !== '' ? $post['modelo'] : null,
            'color'       => trim((string) ($post['color'] ?? '')) ?: null,
            'cilindraje'  => ($post['cilindraje'] ?? '') !== '' ? $post['cilindraje'] : null,
            'kilometraje' => ($post['kilometraje'] ?? '') !== '' ? $post['kilometraje'] : null,
        ];
    }

    public function index()
    {
        $model   = new MotoModel();
        $filtros = [
            'q'          => trim((string) $this->request->getGet('q')),
            'placa'      => trim((string) $this->request->getGet('placa')),
            'id_cliente' => $this->request->getGet('id_cliente'),
        ];

        $data = [
            'titulo'   => 'Motos',
            'motos'    => $model->filtrar($filtros, $this->idClienteScope())->paginate(10),
            'pager'    => $model->pager,
            'filtros'  => $filtros,
            'esAdmin'  => $this->esAdmin(),
            'clientes' => $this->esAdmin() ? (new ClienteModel())->orderBy('nombre_completo', 'ASC')->findAll() : [],
        ];

        return view('motos/index', $data);
    }

    public function show($id)
    {
        $motoModel = new MotoModel();
        $moto      = $motoModel->getDetalle((int) $id, $this->idClienteScope());
        if (!$moto) {
            return redirect()->to('/motos')->with('error', 'Moto no encontrada.');
        }

        return view('motos/show', [
            'titulo'    => 'Detalle de la moto',
            'moto'      => $moto,
            'esAdmin'   => $this->esAdmin(),
            'servicios' => $motoModel->getServicios((int) $id),
            'historial' => $motoModel->getHistorial($moto['placa']),
        ]);
    }

    public function create()
    {
        $data['esAdmin'] = $this->esAdmin();
        // Si es admin, necesita elegir a qué cliente pertenece la moto
        if ($data['esAdmin']) {
            $clienteModel = new ClienteModel();
            $data['clientes'] = $clienteModel->where('id_estado_cliente', ClienteModel::ESTADO_ACTIVO)
                ->orderBy('nombre_completo', 'ASC')->findAll();
        }

        return view('motos/create', $data);
    }

    public function store()
    {
        $rules = $this->reglasComunes() + [
            'placa' => 'required|alpha_numeric|min_length[5]|max_length[15]|is_unique[moto.placa]',
        ];
        if ($this->esAdmin()) {
            $rules['id_cliente'] = 'required|integer|is_not_unique[cliente.id_cliente]';
        }

        // La placa se guarda en mayúsculas: se normaliza antes de validar la unicidad.
        $input          = $this->request->getPost();
        $input['placa'] = strtoupper(trim((string) ($input['placa'] ?? '')));

        if (!$this->validateData($input, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idCliente = $this->esAdmin()
            ? (int) $this->request->getPost('id_cliente')
            : (int) session()->get('id_cliente');

        // La moto debe quedar asociada a un cliente existente y activo
        $cliente = $idCliente > 0 ? (new ClienteModel())->find($idCliente) : null;
        if (!$cliente || (int) $cliente['id_estado_cliente'] !== ClienteModel::ESTADO_ACTIVO) {
            return redirect()->back()->withInput()->with('error', 'La moto debe asociarse a un cliente activo.');
        }

        (new MotoModel())->insert($this->datosComunes() + [
            'placa'      => $input['placa'],
            'id_cliente' => $idCliente,
        ]);

        return redirect()->to('/motos')->with('success', 'Moto registrada con éxito.');
    }

    public function edit($id)
    {
        $moto = $this->buscarMoto((int) $id);
        if (!$moto) {
            return redirect()->to('/motos')->with('error', 'Moto no encontrada.');
        }

        $data['moto'] = $moto;
        return view('motos/edit', $data);
    }

    public function update($id)
    {
        $id   = (int) $id;
        $moto = $this->buscarMoto($id);
        if (!$moto) {
            return redirect()->to('/motos')->with('error', 'Moto no encontrada.');
        }

        if (!$this->validate($this->reglasComunes())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        (new MotoModel())->update($id, $this->datosComunes());

        return redirect()->to('/motos')->with('success', 'Moto actualizada con éxito.');
    }

    public function delete($id)
    {
        $motoModel = new MotoModel();
        $moto      = $this->buscarMoto((int) $id);
        if (!$moto) {
            return redirect()->to('/motos')->with('error', 'Moto no encontrada.');
        }

        if ($motoModel->tieneRelaciones($moto)) {
            return redirect()->to('/motos')->with('error', 'La moto tiene servicios o historial asociados y no se puede eliminar.');
        }

        $motoModel->delete((int) $id);

        return redirect()->to('/motos')->with('success', 'Moto eliminada con éxito.');
    }
}
