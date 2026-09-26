<?php

namespace App\Models;

use CodeIgniter\Model;

class InventarioModel extends Model
{
    protected $table            = 'core.inventario';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = [
        'nombre', 'tipo', 'unidad', 'stock_actual', 'stock_minimo',
        'fecha_vencimiento', 'proveedor_id',
    ];

    /**
     * Solo medicamentos y vitaminas: es lo único que el módulo Clínico
     * puede recetar (alimento y limpieza pertenecen a otros módulos).
     */
    public function listaMedicamentosYVitaminas(): array
    {
        return $this->whereIn('tipo', ['medicamento', 'vitamina'])
                    ->orderBy('nombre', 'ASC')
                    ->findAll();
    }

    /**
     * Descuenta stock de un insumo de forma atómica (evita condiciones de
     * carrera si dos personas registran un tratamiento al mismo tiempo).
     */
    public function descontarStock(int $inventarioId, float $cantidad): bool
    {
        return $this->db->table('core.inventario')
                        ->where('id', $inventarioId)
                        ->set('stock_actual', 'stock_actual - ' . (float) $cantidad, false)
                        ->update();
    }

    /**
     * Lista únicamente los productos de inventario que son alimentos.
     */
    public function listaAlimentos(): array
    {
        return $this->where('tipo', 'alimento')
                    ->orderBy('nombre', 'ASC')
                    ->findAll();
    }

    /**
     * Lista alimentos cuyo stock actual está en o debajo del stock mínimo.
     */
    public function alimentosConStockBajo(): array
    {
        return $this->where('tipo', 'alimento')
                    ->where('stock_actual <= stock_minimo')
                    ->orderBy('nombre', 'ASC')
                    ->findAll();
    }

}
