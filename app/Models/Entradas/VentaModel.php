<?php

namespace App\Models\Entradas;

use CodeIgniter\Model;

/**
 * Venta de entradas (taquilla o portal).
 */
class VentaModel extends Model
{
    protected $table         = 'entradas.ventas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'codigo', 'fecha', 'empleado_id', 'cliente_id', 'punto_venta', 'tipo_pago',
        'tipo_venta', 'subtotal', 'descuento', 'total', 'estado',
        'motivo_anulacion', 'fecha_anulacion', 'anulado_por',
    ];

    protected $validationRules = [
        'codigo'           => 'required|max_length[12]',
        'fecha'            => 'required|valid_date[Y-m-d H:i:s]',
        'empleado_id'      => 'required|is_natural_no_zero',
        'cliente_id'       => 'permit_empty|is_natural_no_zero',
        'punto_venta'      => 'permit_empty|max_length[50]',
        'tipo_pago'        => 'required|in_list[efectivo,tarjeta,transferencia]',
        'tipo_venta'       => 'required|in_list[taquilla,portal]',
        'subtotal'         => 'required|decimal|greater_than_equal_to[0]',
        'descuento'        => 'permit_empty|decimal|greater_than_equal_to[0]',
        'total'            => 'required|decimal|greater_than[0]',
        'estado'           => 'required|in_list[completada,anulada]',
    ];

    protected $validationMessages = [
        'codigo' => [
            'max_length' => 'El código de venta no puede superar 12 caracteres.',
        ],
    ];

    public function conDetalle(int $id): ?array
    {
        $db = db_connect();

        $venta = $db->table('entradas.ventas as v')
            ->select('v.*, e.nombre as empleado_nombre, e.apellido as empleado_apellido, c.nombre as cliente_nombre, c.email as cliente_email')
            ->join('core.empleados as e', 'e.id = v.empleado_id', 'left')
            ->join('entradas.visitantes as c', 'c.id = v.cliente_id', 'left')
            ->where('v.id', $id)
            ->get()
            ->getRowArray();

        if ($venta === null) {
            return null;
        }

        $venta['boletos'] = $db->table('entradas.boletos as b')
            ->select('b.*, t.nombre as tarifa_nombre, t.tipo_visitante, p.nombre as promocion_nombre')
            ->join('entradas.tarifas as t', 't.id = b.tarifa_id')
            ->join('entradas.promociones as p', 'p.id = b.promocion_id', 'left')
            ->where('b.venta_id', $id)
            ->orderBy('b.id', 'asc')
            ->get()
            ->getResultArray();

        return $venta;
    }

    /**
     * Ventas en un rango de fechas con filtros opcionales por método de pago y empleado.
     */
    public function reporte(string $desde, string $hasta, string $tipoPago = '', int $empleadoId = 0): array
    {
        $db = db_connect();
        $qb = $db->table('entradas.ventas as v')
            ->select('v.id, v.codigo, v.fecha, v.tipo_pago, v.tipo_venta, v.subtotal, v.descuento, v.total, v.estado, v.motivo_anulacion, v.fecha_anulacion, e.nombre as empleado_nombre, e.apellido as empleado_apellido')
            ->join('core.empleados as e', 'e.id = v.empleado_id', 'left')
            ->where('v.fecha >=', $desde . ' 00:00:00')
            ->where('v.fecha <=', $hasta . ' 23:59:59')
            ->orderBy('v.fecha', 'desc');

        if ($tipoPago !== '') {
            $qb->where('v.tipo_pago', $tipoPago);
        }
        if ($empleadoId > 0) {
            $qb->where('v.empleado_id', $empleadoId);
        }

        return $qb->get()->getResultArray();
    }

    public function contarBoletos(int $ventaId): int
    {
        $db = db_connect();

        return (int) $db->table('entradas.boletos')
            ->where('venta_id', $ventaId)
            ->countAllResults();
    }
}