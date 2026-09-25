<?php

namespace App\Models;

class InventarioModel extends BaseModel
{
    protected $table = 'core.inventario';
    protected $allowedFields = ['nombre', 'tipo', 'unidad', 'stock_actual', 'stock_minimo', 'fecha_vencimiento', 'proveedor_id'];
}
