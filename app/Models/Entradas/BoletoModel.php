<?php

namespace App\Models\Entradas;

use CodeIgniter\Model;

/**
 * Boleto individual emitido dentro de una venta.
 */
class BoletoModel extends Model
{
    protected $table         = 'entradas.boletos';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'venta_id', 'tarifa_id', 'promocion_id', 'fecha_visita', 'codigo_qr', 'precio', 'estado',
    ];

    protected $validationRules = [
        'venta_id'      => 'required|is_natural_no_zero',
        'tarifa_id'     => 'required|is_natural_no_zero',
        'promocion_id'  => 'permit_empty|is_natural_no_zero',
        'fecha_visita'  => 'required|valid_date[Y-m-d]',
        'codigo_qr'     => 'required|max_length[100]',
        'precio'        => 'required|decimal|greater_than_equal_to[0]',
        'estado'        => 'required|in_list[emitido,usado,anulado]',
    ];

    public function porCodigo(string $codigo): ?array
    {
        $db = db_connect();

        $boleto = $db->table('entradas.boletos as b')
            ->select('b.*, t.nombre as tarifa_nombre, t.tipo_visitante, p.nombre as promocion_nombre, v.codigo as venta_codigo, v.fecha as venta_fecha, v.tipo_venta')
            ->join('entradas.tarifas as t', 't.id = b.tarifa_id')
            ->join('entradas.promociones as p', 'p.id = b.promocion_id', 'left')
            ->join('entradas.ventas as v', 'v.id = b.venta_id')
            ->where('b.codigo_qr', $codigo)
            ->get()
            ->getRowArray();

        return $boleto === null ? null : $boleto;
    }

    public function marcarUsado(int $id): void
    {
        $this->update($id, ['estado' => 'usado']);
    }
}