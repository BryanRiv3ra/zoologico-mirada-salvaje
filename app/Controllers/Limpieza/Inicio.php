<?php

namespace App\Controllers\Limpieza;

use App\Controllers\BaseController;

/**
 * Punto de entrada del módulo de Limpieza: redirige según el rol del usuario.
 */
class Inicio extends BaseController
{
    public function index()
    {
        $roles = (array) session('roles');

        if (in_array('empleado_limpieza', $roles, true)) {
            return redirect()->to('limpieza/mis-tareas');
        }

        if (in_array('supervisor', $roles, true)) {
            return redirect()->to('limpieza/seguimiento');
        }

        return redirect()->to('limpieza/zonas');
    }
}