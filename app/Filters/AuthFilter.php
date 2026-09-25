<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filtro de autenticación (provisional).
 *
 * Protege las rutas de los módulos Limpieza y Entradas exigiendo sesión activa.
 * La autenticación completa (login, roles, seeder) la implementa el integrante
 * del módulo core-autenticacion; este filtro conserva la misma interfaz prevista
 * allí y debe ser reemplazado al integrar esa rama.
 */
class AuthFilter implements FilterInterface
{
    /**
     * Exige que exista `usuario_id` en la sesión.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|null
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session('usuario_id') === null) {
            return redirect()->to('/')->with('error', 'Debes iniciar sesión para acceder a este módulo.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
    }
}