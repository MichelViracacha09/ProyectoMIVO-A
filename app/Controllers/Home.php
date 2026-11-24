<?php

namespace App\Controllers;

use App\Models\CitaModel;
use App\Models\ClienteModel;
use App\Models\ModuloModel;
use App\Models\MotoModel;
use App\Models\ServicioModel;

class Home extends BaseController
{
    public function index()
    {
        $idRol = (int) session()->get('id_rol_num');
        $moduloModel = new ModuloModel();
        $modulos = $moduloModel->getModulosByRol($idRol);

        $data = [
            'nombre'   => session()->get('nombre'),
            'rol'      => session()->get('rol'),
            'modulos'  => $modulos,
            'esAdmin'  => (bool) session()->get('es_admin'),
            'stats'    => $this->estadisticas(),
        ];

        return view('dashboard/home', $data);
    }

    /**
     * Indicadores reales según el perfil: el administrador ve todo el taller;
     * el cliente, solo lo suyo. Cada indicador exige el permiso "ver" del módulo.
     */
    private function estadisticas(): array
    {
        $esAdmin   = (bool) session()->get('es_admin');
        $idCliente = $esAdmin ? null : (int) session()->get('id_cliente');
        $stats     = [];

        if (puede('clientes', 'ver') && $esAdmin) {
            $stats['clientes'] = (new ClienteModel())->countAllResults();
        }
        if (puede('motos', 'ver')) {
            $motos = new MotoModel();
            $stats['motos'] = $esAdmin ? $motos->countAllResults() : $motos->where('id_cliente', $idCliente)->countAllResults();
        }
        if (puede('citas', 'ver')) {
            $porEstado = (new CitaModel())->contarPorEstado($idCliente);
            $stats['citas']        = array_sum($porEstado);
            $stats['citas_estado'] = $porEstado;
        }
        if (puede('servicios', 'ver')) {
            $stats['servicios'] = (new ServicioModel())->countAllResults();
        }

        return $stats;
    }
}
