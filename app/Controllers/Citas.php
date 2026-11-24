<?php

namespace App\Controllers;

use App\Models\CitaModel;
use App\Models\ClienteModel;
use App\Models\ServicioModel;

/**
 * Gestión administrativa de citas. Los permisos (ver/crear/editar/eliminar) se validan
 * con los filtros 'permiso:citas,<accion>' de Config/Routes.php (tabla modulo_rol).
 * La gestión técnica de la cita (diagnóstico, ejecución) pertenece al módulo Node.js.
 */
class Citas extends BaseController
{
    private const REGLA_HORA = 'regex_match[/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/]';

    private function esAdmin(): bool
    {
        return (bool) session()->get('es_admin');
    }

    /** Id del cliente al que se restringe la consulta; null para el administrador. */
    private function idClienteScope(): ?int
    {
        return $this->esAdmin() ? null : (int) session()->get('id_cliente');
    }

    private function buscarCita(int $id): ?array
    {
        return (new CitaModel())->getDetalle($id, $this->idClienteScope());
    }

    private function catalogos(): array
    {
        return [
            'servicios' => (new ServicioModel())->orderBy('nombre_servicio', 'ASC')->findAll(),
            'clientes'  => $this->esAdmin()
                ? (new ClienteModel())->where('id_estado_cliente', ClienteModel::ESTADO_ACTIVO)->orderBy('nombre_completo', 'ASC')->findAll()
                : [],
            'esAdmin'   => $this->esAdmin(),
        ];
    }

    public function index()
    {
        $model   = new CitaModel();
        $filtros = [
            'q'           => trim((string) $this->request->getGet('q')),
            'estado'      => $this->request->getGet('estado'),
            'fecha_desde' => $this->request->getGet('fecha_desde'),
            'fecha_hasta' => $this->request->getGet('fecha_hasta'),
            'id_cliente'  => $this->request->getGet('id_cliente'),
        ];

        $data = [
            'titulo'  => 'Citas',
            'citas'   => $model->filtrar($filtros, $this->idClienteScope())->paginate(10),
            'pager'   => $model->pager,
            'filtros' => $filtros,
            'estados' => CitaModel::ESTADOS,
        ] + $this->catalogos();

        return view('citas/index', $data);
    }

    public function show($id)
    {
        $cita = $this->buscarCita((int) $id);
        if (!$cita) {
            return redirect()->to('/citas')->with('error', 'Cita no encontrada.');
        }

        return view('citas/show', [
            'titulo'      => 'Detalle de la cita',
            'cita'        => $cita,
            'esAdmin'     => $this->esAdmin(),
            'cancelable'  => !in_array(strtoupper($cita['estado_cita']), CitaModel::ESTADOS_FINALES, true),
        ]);
    }

    public function create()
    {
        return view('citas/create', $this->catalogos());
    }

    public function store()
    {
        $rules = [
            'fecha_cita'    => 'required|valid_date[Y-m-d]',
            'hora_cita'     => ['required', self::REGLA_HORA],
            'id_servicio'   => 'required|integer|is_not_unique[servicio.id_servicio]',
            'observaciones' => 'permit_empty|max_length[255]',
        ];
        if ($this->esAdmin()) {
            $rules['id_cliente'] = 'required|integer|is_not_unique[cliente.id_cliente]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idCliente = $this->esAdmin()
            ? (int) $this->request->getPost('id_cliente')
            : (int) session()->get('id_cliente');

        // Solo un cliente (o un administrador eligiendo al cliente) puede agendar; evita violar la FK.
        $cliente = $idCliente > 0 ? (new ClienteModel())->find($idCliente) : null;
        if (!$cliente || (int) $cliente['id_estado_cliente'] !== ClienteModel::ESTADO_ACTIVO) {
            return redirect()->back()->withInput()->with('error', 'Solo un cliente activo puede tener citas.');
        }

        $fecha = $this->request->getPost('fecha_cita');
        $hora  = $this->request->getPost('hora_cita');

        if ($fecha < date('Y-m-d')) {
            return redirect()->back()->withInput()->with('errors', ['fecha_cita' => 'La fecha de la cita no puede ser anterior a hoy.']);
        }

        $citaModel = new CitaModel();
        if ($citaModel->hayConflicto($fecha, $hora)) {
            return redirect()->back()->withInput()->with('errors', ['hora_cita' => 'Ya existe una cita en esa fecha y hora. Elija otro horario.']);
        }

        $citaModel->insert([
            'fecha_cita'          => $fecha,
            'hora_cita'           => $hora,
            'servicio_a_realizar' => trim((string) $this->request->getPost('observaciones')) ?: null,
            'estado_cita'         => 'PENDIENTE',
            'id_cliente'          => $idCliente,
            'id_servicio'         => (int) $this->request->getPost('id_servicio'),
        ]);

        return redirect()->to('/citas')->with('success', 'Cita agendada con éxito.');
    }

    public function edit($id)
    {
        $cita = $this->buscarCita((int) $id);
        if (!$cita) {
            return redirect()->to('/citas')->with('error', 'Cita no encontrada.');
        }

        return view('citas/edit', ['cita' => $cita, 'esAdmin' => $this->esAdmin()]);
    }

    public function update($id)
    {
        $id   = (int) $id;
        $cita = $this->buscarCita($id);
        if (!$cita) {
            return redirect()->to('/citas')->with('error', 'Cita no encontrada.');
        }

        $rules = [
            'fecha_cita'    => 'required|valid_date[Y-m-d]',
            'hora_cita'     => ['required', self::REGLA_HORA],
            'observaciones' => 'permit_empty|max_length[255]',
        ];
        if ($this->esAdmin()) {
            $rules['estado_cita'] = 'required|in_list[' . implode(',', CitaModel::ESTADOS) . ']';
        }
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fecha  = $this->request->getPost('fecha_cita');
        $hora   = $this->request->getPost('hora_cita');
        $estado = $this->esAdmin() ? $this->request->getPost('estado_cita') : $cita['estado_cita'];

        $cambioHorario = $fecha !== $cita['fecha_cita'] || date('H:i:s', strtotime($hora)) !== $cita['hora_cita'];
        if ($cambioHorario && $estado !== 'CANCELADA') {
            if ($fecha < date('Y-m-d')) {
                return redirect()->back()->withInput()->with('errors', ['fecha_cita' => 'La fecha de la cita no puede ser anterior a hoy.']);
            }
            if ((new CitaModel())->hayConflicto($fecha, $hora, $id)) {
                return redirect()->back()->withInput()->with('errors', ['hora_cita' => 'Ya existe una cita en esa fecha y hora. Elija otro horario.']);
            }
        }

        (new CitaModel())->update($id, [
            'fecha_cita'          => $fecha,
            'hora_cita'           => $hora,
            'servicio_a_realizar' => trim((string) $this->request->getPost('observaciones')) ?: null,
            'estado_cita'         => $estado,
        ]);

        return redirect()->to('/citas')->with('success', 'Cita actualizada con éxito.');
    }

    /**
     * Cancela la cita (estado CANCELADA). El cliente solo puede cancelar las suyas.
     */
    public function cancel($id)
    {
        $id   = (int) $id;
        $cita = $this->buscarCita($id);
        if (!$cita) {
            return redirect()->to('/citas')->with('error', 'Cita no encontrada.');
        }

        if (in_array(strtoupper($cita['estado_cita']), CitaModel::ESTADOS_FINALES, true)) {
            return redirect()->to('/citas')->with('error', 'La cita ya está ' . strtolower($cita['estado_cita']) . ' y no se puede cancelar.');
        }

        (new CitaModel())->update($id, ['estado_cita' => 'CANCELADA']);

        return redirect()->to('/citas')->with('success', 'Cita cancelada.');
    }

    public function delete($id)
    {
        $id   = (int) $id;
        $cita = $this->buscarCita($id);
        if (!$cita) {
            return redirect()->to('/citas')->with('error', 'Cita no encontrada.');
        }

        $historial = \Config\Database::connect()->table('historial_de_servicios')->where('id_citas', $id)->countAllResults();
        if ($historial > 0) {
            return redirect()->to('/citas')->with('error', 'La cita tiene historial de servicio asociado y no se puede eliminar.');
        }

        (new CitaModel())->delete($id);

        return redirect()->to('/citas')->with('success', 'Cita eliminada con éxito.');
    }
}
