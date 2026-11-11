<?php

namespace App\Controllers;

use App\Models\ModuloModel;

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
            'modulos'  => $modulos
        ];

        return view('dashboard/home', $data);
    }
}