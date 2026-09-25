<?php

namespace App\Models;

class InventarioModel extends BaseModel
{
    protected $table = 'core.inventario';
    protected $allowedFields = ['nombre', 'tipo', 'unidad', 'stock_actual', 'stock_minimo', 'fecha_vencimiento', 'proveedor_id'];
    protected $validationRules = [
        'nombre' => 'required|max_length[150]',
        'tipo' => 'required|in_list[alimento,medicamento,limpieza,vitamina]',
        'unidad' => 'required|max_length[30]',
        'stock_actual' => 'permit_empty|decimal',
        'stock_minimo' => 'permit_empty|decimal',
        'fecha_vencimiento' => 'permit_empty|valid_date[Y-m-d]',
        'proveedor_id' => 'permit_empty|is_natural_no_zero',
    ];
}
