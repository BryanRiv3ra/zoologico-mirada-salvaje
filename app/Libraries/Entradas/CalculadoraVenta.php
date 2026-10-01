<?php

namespace App\Libraries\Entradas;

/**
 * Cálculo de importes de una venta a partir de sus líneas.
 *
 * Cada línea es un arreglo:
 *   [
 *     'tarifa_id'  => int,
 *     'precio'     => float,        // precio de tarifa (antes de descuento)
 *     'cantidad'   => int,          // boletos de esa tarifa
 *     'descuento'  => float,        // porcentaje 0-100 (0 si no aplica promoción)
 *   ]
 */
class CalculadoraVenta
{
    public const MAX_DESCUENTO = 100;

    /**
     * @return array{totales: array<string,float|int>, lineas: list<array<string,mixed>>}
     */
    public function calcular(array $items): array
    {
        $cantidadBoletos = 0;
        $subtotal        = 0.0;
        $descuentoTotal  = 0.0;
        $total           = 0.0;
        $lineas          = [];

        foreach ($items as $item) {
            $precio  = round((float) ($item['precio'] ?? 0), 2);
            $cantidad = max(1, (int) ($item['cantidad'] ?? 1));
            $descuento = (float) ($item['descuento'] ?? 0);

            if ($descuento < 0 || $descuento > self::MAX_DESCUENTO) {
                $descuento = 0.0;
            }

            $importe    = $precio * $cantidad;
            $descLinea  = round($importe * ($descuento / self::MAX_DESCUENTO), 2);
            $totalLinea = round($importe - $descLinea, 2);

            $subtotal       += $importe;
            $descuentoTotal += $descLinea;
            $total          += $totalLinea;
            $cantidadBoletos += $cantidad;

            $lineas[] = [
                'tarifa_id'          => (int) $item['tarifa_id'],
                'nombre'             => $item['nombre'] ?? '',
                'precio'             => $precio,
                'precioUnitario'     => round($precio * (1 - $descuento / self::MAX_DESCUENTO), 2),
                'cantidad'           => $cantidad,
                'descuentoPorcentual' => $descuento,
                'importe'            => round($importe, 2),
                'descuentoImporte'   => $descLinea,
                'totalLinea'         => $totalLinea,
            ];
        }

        return [
            'totales' => [
                'subtotal'        => round($subtotal, 2),
                'descuento'       => round($descuentoTotal, 2),
                'total'           => round($total, 2),
                'cantidadBoletos' => $cantidadBoletos,
            ],
            'lineas' => $lineas,
        ];
    }
}