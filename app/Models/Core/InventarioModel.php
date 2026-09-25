<?php

namespace App\Models\Core;

use CodeIgniter\Model;

/**
 * Modelo de la tabla compartida core.inventario.
 */
class InventarioModel extends Model
{
    protected $table         = 'core.inventario';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['nombre', 'tipo', 'unidad', 'stock_actual', 'stock_minimo', 'fecha_vencimiento', 'proveedor_id'];

    protected $validationRules = [
        'nombre'       => 'required|max_length[150]',
        'tipo'         => 'required|in_list[alimento,medicamento,limpieza,vitamina]',
        'unidad'       => 'required|max_length[30]',
        'stock_actual' => 'permit_empty|decimal',
        'stock_minimo' => 'permit_empty|decimal',
    ];

    /**
     * Insumos disponibles para labores de limpieza (tipo = 'limpieza').
     */
    public function insumosLimpieza(): array
    {
        return $this->where('tipo', 'limpieza')->orderBy('nombre', 'asc')->findAll();
    }
}