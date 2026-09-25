<?php

namespace App\Libraries\Entradas;

/**
 * Pasarela de pago SIMULADA.
 *
 * Acepta cualquier operación y devuelve un estado aprobado con referencia.
 * Su única regla real es rechazar montos no positivos. Es el punto único de
 * intercambio cuando exista una pasarela real (devolver respuesta JSON).
 */
class PasarelaPagoSimulada
{
    public function cobrar(float $monto, string $metodo): array
    {
        if ($monto <= 0) {
            throw new PagoException('El monto a cobrar debe ser mayor a cero.');
        }

        return [
            'estado'    => 'pagado',
            'metodo'    => $metodo,
            'referencia' => 'SIM-' . strtoupper(bin2hex(random_bytes(4))),
            'fecha'     => date('Y-m-d H:i:s'),
        ];
    }
}