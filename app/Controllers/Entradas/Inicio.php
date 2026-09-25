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

        // Taquilla: quienes venden boletos día a día.
        if (array_intersect(['cajero', 'administrador', 'supervisor'], $roles) !== []) {
            return redirect()->to('/entradas/taquilla');
        }

        // Mercadeo administra tarifas.
        if (in_array('admin_mercadeo', $roles, true)) {
            return redirect()->to('/entradas/tarifas');
        }

        // control_acceso: su pantalla no está disponible todavía (pendiente
        // de ampliar el modelo de datos). Se redirige a reportes mientras tanto.
        return redirect()->to('/entradas/reportes');
    }
}