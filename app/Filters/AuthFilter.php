<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filtro de autenticación.
 *
 * Protege las rutas de los módulos exigiendo sesión activa. Cuando no la hay,
 * recuerda la URL solicitada en session('redirect_to') para que Auth::autenticar()
 * devuelva al usuario al mismo módulo después de iniciar sesión.
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
            session()->set('redirect_to', current_url());

            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para acceder a este módulo.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
    }
}
