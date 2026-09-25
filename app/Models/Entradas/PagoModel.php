<?php

namespace App\Models\Entradas;

use CodeIgniter\Model;

/**
 * Pagos asociados a una venta (método simulado en portal).
 */
class PagoModel extends Model
{
    protected $table         = 'entradas.pagos';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['venta_id', 'metodo', 'monto', 'estado', 'referencia'];

    protected $validationRules = [
        'venta_id'   => 'required|is_natural_no_zero',
        'metodo'     => 'required|max_length[50]',
        'monto'      => 'required|decimal|greater_than[0]',
        'estado'     => 'required|in_list[pendiente,pagado,rechazado]',
        'referencia' => 'permit_empty|max_length[100]',
    ];

    public function deVenta(int $ventaId): array
    {
        return $this->where('venta_id', $ventaId)->findAll();
    }
}