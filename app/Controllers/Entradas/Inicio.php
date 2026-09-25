<?php

namespace App\Controllers\Entradas;

use App\Controllers\BaseController;

/**
 * Inicio del módulo: redirige según el rol del usuario.
 */
class Inicio extends BaseController
{
    public function index()
    {
        $roles = (array) session('roles');

        if (in_array('cajero', $roles, true)) {
            return redirect()->to('/entradas/taquilla');
        }
        if (in_array('control_acceso', $roles, true)) {
            return redirect()->to('/entradas/acceso');
        }
        if (in_array('admin_mercadeo', $roles, true)) {
            return redirect()->to('/entradas/promociones');
        }

        return redirect()->to('/entradas/reportes');
    }
}