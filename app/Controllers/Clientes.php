<?php

namespace App\Controllers;

use App\Models\CitaModel;
use App\Models\ClienteModel;
use App\Models\EstadoClienteModel;
use App\Models\MotoModel;
use App\Models\TipoIdentificacionModel;
use App\Models\UsuarioModel;

/**
 * Gestión administrativa de clientes. El acceso lo controlan los filtros
 * 'permiso:clientes,<accion>' definidos en Config/Routes.php (tabla modulo_rol).
 */
class Clientes extends BaseController
{
    private function reglas(?int $id = null): array
    {
        $ignorar = $id !== null ? ',id_cliente,' . $id : '';

        $reglas = [
            'nombre_completo'       => 'required|min_length[3]|max_length[150]',
            'numero_identificacion' => 'required|alpha_numeric|max_length[30]|is_unique[cliente.numero_identificacion' . $ignorar . ']',
            'numero_celular'        => 'required|regex_match[/^\+?[0-9]{7,15}$/]',
            'correo_electronico'    => 'required|valid_email|max_length[150]|is_unique[cliente.correo_electronico' . $ignorar . ']',
            'direccion'             => 'permit_empty|max_length[255]',
            'fecha_nacimiento'      => 'permit_empty|valid_date[Y-m-d]',
            'id_identificacion'     => 'required|integer|is_not_unique[tipo_identificacion.id_identificacion]',
        ];
        if ($id !== null) {
            $reglas['id_estado_cliente'] = 'required|integer|is_not_unique[estado_cliente.id_estado_cliente]';
        } else {
            $reglas['password'] = 'permit_empty|min_length[8]|max_length[72]';
        }

        return $reglas;
    }

    /** El correo también sirve para iniciar sesión: no puede coincidir con el de un administrador. */
    private function correoLibreDeAdmin(string $correo): bool
    {
        return \Config\Database::connect()->table('administrador')->where('correo', $correo)->countAllResults() === 0;
    }

    private function datosForm(): array
    {
        return [
            'nombre_completo'       => trim((string) $this->request->getPost('nombre_completo')),
            'numero_identificacion' => trim((string) $this->request->getPost('numero_identificacion')),
            'numero_celular'        => trim((string) $this->request->getPost('numero_celular')),
            'correo_electronico'    => strtolower(trim((string) $this->request->getPost('correo_electronico'))),
            'direccion'             => trim((string) $this->request->getPost('direccion')) ?: null,
            'fecha_nacimiento'      => $this->request->getPost('fecha_nacimiento') ?: null,
            'id_identificacion'     => (int) $this->request->getPost('id_identificacion'),
        ];
    }

    private function catalogos(): array
    {
        return [
            'tipos_doc' => (new TipoIdentificacionModel())->findAll(),
            'estados'   => (new EstadoClienteModel())->findAll(),
        ];
    }

    public function index()
    {
        $model   = new ClienteModel();
        $filtros = [
            'q'                 => trim((string) $this->request->getGet('q')),
            'id_estado_cliente' => $this->request->getGet('id_estado_cliente'),
        ];

        $data = [
            'titulo'   => 'Clientes',
            'clientes' => $model->filtrar($filtros)->paginate(10),
            'pager'    => $model->pager,
            'filtros'  => $filtros,
        ] + $this->catalogos();

        return view('clientes/index', $data);
    }

    public function show($id)
    {
        $cliente = (new ClienteModel())->getDetalle((int) $id);
        if (!$cliente) {
            return redirect()->to('/clientes')->with('error', 'Cliente no encontrado.');
        }

        return view('clientes/show', [
            'titulo'  => 'Detalle del cliente',
            'cliente' => $cliente,
            'motos'   => (new MotoModel())->getByCliente((int) $id),
            'citas'   => (new CitaModel())->getByCliente((int) $id),
        ]);
    }

    public function create()
    {
        return view('clientes/create', ['titulo' => 'Nuevo cliente'] + $this->catalogos());
    }

    public function store()
    {
        if (!$this->validate($this->reglas())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $datos = $this->datosForm();
        if (!$this->correoLibreDeAdmin($datos['correo_electronico'])) {
            return redirect()->back()->withInput()->with('errors', ['correo_electronico' => 'Ese correo pertenece a otro usuario del sistema.']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $idUsuario = null;
        $password  = (string) $this->request->getPost('password');
        if ($password !== '') {
            // Cuenta de acceso opcional para el cliente (rol Cliente)
            $idUsuario = (new UsuarioModel())->insert([
                'hash'   => password_hash($password, PASSWORD_BCRYPT),
                'estado' => 'ACTIVO',
                'id_rol' => 3,
            ], true);
        }

        (new ClienteModel())->insert($datos + [
            'id_estado_cliente' => ClienteModel::ESTADO_ACTIVO,
            'id_usuario'        => $idUsuario,
        ]);

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'No se pudo registrar el cliente.');
        }

        return redirect()->to('/clientes')->with('success', 'Cliente registrado con éxito.');
    }

    public function edit($id)
    {
        $cliente = (new ClienteModel())->find((int) $id);
        if (!$cliente) {
            return redirect()->to('/clientes')->with('error', 'Cliente no encontrado.');
        }

        return view('clientes/edit', ['titulo' => 'Editar cliente', 'cliente' => $cliente] + $this->catalogos());
    }

    public function update($id)
    {
        $id      = (int) $id;
        $model   = new ClienteModel();
        $cliente = $model->find($id);
        if (!$cliente) {
            return redirect()->to('/clientes')->with('error', 'Cliente no encontrado.');
        }

        if (!$this->validate($this->reglas($id))) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $datos = $this->datosForm();
        if (!$this->correoLibreDeAdmin($datos['correo_electronico'])) {
            return redirect()->back()->withInput()->with('errors', ['correo_electronico' => 'Ese correo pertenece a otro usuario del sistema.']);
        }

        $model->update($id, $datos + ['id_estado_cliente' => (int) $this->request->getPost('id_estado_cliente')]);

        return redirect()->to('/clientes')->with('success', 'Cliente actualizado con éxito.');
    }

    /**
     * Activa o inactiva (alterna Activo <-> Inactivo). Un cliente Bloqueado pasa a Activo.
     */
    public function toggleEstado($id)
    {
        $model   = new ClienteModel();
        $cliente = $model->find((int) $id);
        if (!$cliente) {
            return redirect()->to('/clientes')->with('error', 'Cliente no encontrado.');
        }

        $nuevo = (int) $cliente['id_estado_cliente'] === ClienteModel::ESTADO_ACTIVO
            ? ClienteModel::ESTADO_INACTIVO
            : ClienteModel::ESTADO_ACTIVO;
        $model->update((int) $id, ['id_estado_cliente' => $nuevo]);

        return redirect()->back()->with('success', $nuevo === ClienteModel::ESTADO_ACTIVO ? 'Cliente activado.' : 'Cliente inactivado.');
    }

    public function delete($id)
    {
        $model   = new ClienteModel();
        $cliente = $model->find((int) $id);
        if (!$cliente) {
            return redirect()->to('/clientes')->with('error', 'Cliente no encontrado.');
        }

        if ($model->tieneRelaciones((int) $id)) {
            return redirect()->to('/clientes')->with('error', 'El cliente tiene motos, citas o facturas asociadas. Inactívelo en lugar de eliminarlo.');
        }

        $db = \Config\Database::connect();
        $db->transStart();
        $model->delete((int) $id);
        if (!empty($cliente['id_usuario'])) {
            (new UsuarioModel())->delete((int) $cliente['id_usuario']);
        }
        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->to('/clientes')->with('error', 'No se pudo eliminar el cliente.');
        }

        return redirect()->to('/clientes')->with('success', 'Cliente eliminado con éxito.');
    }
}
