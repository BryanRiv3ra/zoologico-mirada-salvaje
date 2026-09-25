<?php

namespace App\Models;

class MovimientoInventarioModel extends BaseModel
{
    protected $table = 'core.movimientos_inventario';
    protected $allowedFields = ['inventario_id', 'tipo_movimiento', 'cantidad', 'motivo', 'usuario_id'];
    protected $validationRules = [
        'inventario_id' => 'required|is_natural_no_zero',
        'tipo_movimiento' => 'required|in_list[entrada,salida]',
        'cantidad' => 'required|decimal',
        'motivo' => 'permit_empty|max_length[255]',
        'usuario_id' => 'permit_empty|is_natural_no_zero',
    ];
}
