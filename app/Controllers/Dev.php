<?php

namespace App\Controllers;

/**
 * Controlador SOLO para desarrollo/integración.
 *
 * Crea una sesión simulada con un rol para poder probar los filtros de rol
 * antes de que exista la autenticación real (módulo core-autenticacion).
 * ⚠️ Este controlador NO debe estar disponible en producción: sus rutas solo
 * se registran cuando CI_ENVIRONMENT = development y este archivo debe
 * eliminarse al integrar la autenticación definitiva.
 */
class Dev extends BaseController
{
    protected const ROLES_VALIDOS = [
        'administrador',
        'supervisor',
        'empleado_limpieza',
        'admin_mercadeo',
        'cajero',
        'control_acceso',
    ];

    public function sesion(string $rol)
    {
        if (ENVIRONMENT !== 'development') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! in_array($rol, self::ROLES_VALIDOS, true)) {
            return redirect()->to('/')->with('error', 'Rol de prueba no válido: ' . esc($rol));
        }

        session()->set([
            'usuario_id'  => 1,
            'empleado_id' => 1,
            'nombre'      => 'Usuario de Prueba',
            'email'       => 'dev@prueba.local',
            'roles'       => [$rol],
        ]);

        return redirect()->to('/')->with('success', 'Sesión de prueba activada como: ' . $rol);
    }

    public function cerrar()
    {
        if (ENVIRONMENT !== 'development') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        session()->destroy();

        return redirect()->to('/');
    }
}