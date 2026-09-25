<?php

namespace App\Libraries\Limpieza;

/**
 * Excepción de dominio de asignaciones de limpieza.
 * Si $codigoHttp === 403 la causa es propiedad ajena / falta de permiso.
 */
class AsignacionException extends \RuntimeException
{
    public function __construct(string $mensaje, int $codigoHttp = 400)
    {
        parent::__construct($mensaje, $codigoHttp);
    }
}