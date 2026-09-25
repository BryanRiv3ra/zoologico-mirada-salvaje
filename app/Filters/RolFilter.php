<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filtro de autorizaci├│n por rol (provisional).
 *
 * Uso en rutas: `'filter' => 'rol:administrador,supervisor'`.
 * Compara los roles guardados en la sesi├│n contra los permitidos.
 * La autenticaci├│n completa la implementa el integrante de core-autenticacion;
 * este filtro conserva la misma interfaz prevista all├¡.
 */
class RolFilter implements FilterInterface
{
    /**
     * @param string[]|null $arguments Roles permitidos para la ruta.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session('usuario_id') === null) {
            return redirect()->to('/')->with('error', 'Debes iniciar sesi├│n para acceder a este m├│dulo.');
        }

        $rolesUsuario = (array) session('roles');
        $permitidos   = (array) $arguments;

        if ($permitidos !== [] && count(array_intersect($rolesUsuario, $permitidos)) === 0) {
            $usuario = [
                'nombre'  => session('nombre') ?? 'Usuario',
                'roles'   => $rolesUsuario,
                'rolesOk' => $permitidos,
            ];

            return service('response')
                ->setStatusCode(403)
                ->setBody(view('errors/prohibido', $usuario));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
    }
}