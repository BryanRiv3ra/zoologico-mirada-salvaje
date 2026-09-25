<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filtro de autenticaci├│n (provisional).
 *
 * Protege las rutas de los m├│dulos Limpieza y Entradas exigiendo sesi├│n activa.
 * La autenticaci├│n completa (login, roles, seeder) la implementa el integrante
 * del m├│dulo core-autenticacion; este filtro conserva la misma interfaz prevista
 * all├¡ y debe ser reemplazado al integrar esa rama.
 */
class AuthFilter implements FilterInterface
{
    /**
     * Exige que exista `usuario_id` en la sesi├│n.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|null
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session('usuario_id') === null) {
            return redirect()->to('/')->with('error', 'Debes iniciar sesi├│n para acceder a este m├│dulo.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
    }
}