<?php

namespace App\Models\Entradas;

use CodeIgniter\Model;

/**
 * Adaptado al modelo E-R real: no existe tabla "ventas" ni "boletos"
 * independientes. Cada fila de entradas.entradas ES un boleto individual
 * con su propio pago en entradas.pagos. No se agrupan boletos en una venta.
 */
class VentaModel extends Model
{
    protected $table      = 'entradas.entradas';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    public function conDetalle(int $entradaId): ?array
    {
        $db = db_connect();

        $boleto = $db->table('entradas.entradas as e')
            ->select('e.*, t.nombre as tarifa_nombre, t.tipo_visitante, t.precio as precio_lista, p.nombre as promocion_nombre, v.nombre as visitante_nombre, v.email as visitante_email')
            ->join('entradas.tarifas as t', 't.id = e.tarifa_id')
            ->join('entradas.promociones as p', 'p.id = e.promocion_id', 'left')
            ->join('entradas.visitantes as v', 'v.id = e.visitante_id')
            ->where('e.id', $entradaId)
            ->get()
            ->getRowArray();

        if ($boleto === null) {
            return null;
        }

        $pago = $db->table('entradas.pagos')
            ->where('entrada_id', $entradaId)
            ->orderBy('id', 'desc')
            ->get()
            ->getRowArray();

        $precioLista = (float) $boleto['precio_lista'];
        $total       = (float) $boleto['total'];

        return [
            'id'                => $boleto['id'],
            'codigo'            => 'EN-' . str_pad((string) $boleto['id'], 6, '0', STR_PAD_LEFT),
            'fecha'             => $boleto['fecha_compra'],
            'empleado_nombre'   => '',
            'empleado_apellido' => '',
            'visitante_nombre'  => $boleto['visitante_nombre'],
            'visitante_email'   => $boleto['visitante_email'],
            'tipo_venta'        => 'taquilla',
            'tipo_pago'         => $pago['metodo'] ?? '—',
            'subtotal'          => $precioLista,
            'descuento'         => $precioLista - $total,
            'total'             => $total,
            'estado'            => $this->estadoParaVista($pago['estado'] ?? 'pendiente'),
            'motivo_anulacion'  => ($pago['estado'] ?? '') === 'rechazado' ? 'Boleto anulado' : null,
            'fecha_anulacion'   => null,
            'boletos'           => [[
                'tarifa_nombre'    => $boleto['tarifa_nombre'],
                'tipo_visitante'   => $boleto['tipo_visitante'],
                'fecha_visita'     => $boleto['fecha_visita'],
                'precio'           => $total,
                'promocion_nombre' => $boleto['promocion_nombre'],
                'codigo_qr'        => $boleto['codigo_qr'],
            ]],
        ];
    }

    public function reporte(string $desde, string $hasta, string $tipoPago = '', int $empleadoId = 0): array
{
    $db = db_connect();

    $qb = $db->table('entradas.entradas as e')
        ->select('e.id, e.fecha_compra as fecha, e.total, t.precio as precio_lista, p.metodo as tipo_pago, p.estado as pago_estado')
        ->join('entradas.tarifas as t', 't.id = e.tarifa_id')
        ->join('entradas.pagos as p', 'p.entrada_id = e.id', 'left')
        ->where('e.fecha_compra >=', $desde . ' 00:00:00')
        ->where('e.fecha_compra <=', $hasta . ' 23:59:59')
        ->orderBy('e.fecha_compra', 'desc');

    if ($tipoPago !== '') {
        $qb->where('p.metodo', $tipoPago);
    }
    // $empleadoId no aplica: entradas.entradas no registra empleado (no está en el E-R).

    $filas = $qb->get()->getResultArray();

    return array_map(function (array $fila): array {
        $subtotal = (float) $fila['precio_lista'];
        $total    = (float) $fila['total'];

        return [
            'id'                => $fila['id'],
            'codigo'            => 'EN-' . str_pad((string) $fila['id'], 6, '0', STR_PAD_LEFT),
            'fecha'             => $fila['fecha'],
            'empleado_nombre'   => '',
            'empleado_apellido' => '',
            'tipo_venta'        => 'taquilla',
            'tipo_pago'         => $fila['tipo_pago'] ?? '—',
            'subtotal'          => $subtotal,
            'descuento'         => $subtotal - $total,
            'total'             => $total,
            'estado'            => $this->estadoParaVista($fila['pago_estado'] ?? 'pendiente'),
            'motivo_anulacion'  => ($fila['pago_estado'] ?? '') === 'rechazado' ? 'Boleto anulado' : null,
        ];
    }, $filas);
}
    /**
     * Traduce el estado real del pago (pendiente/pagado/rechazado, según
     * el CHECK de entradas.pagos) al vocabulario que ya usan las vistas
     * (completada/anulada), para no reescribirlas.
     */
    private function estadoParaVista(string $estadoPago): string
    {
        return $estadoPago === 'rechazado' ? 'anulada' : 'completada';
    }

        /**
     * En este modelo cada "venta" YA es un solo boleto (no hay agrupación),
     * así que siempre es 1. Se mantiene el método por compatibilidad con
     * Reportes.php, que lo llama por cada venta completada.
     */
    public function contarBoletos(int $entradaId): int
    {
        return 1;
    }
}