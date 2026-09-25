<?php

namespace App\Models;

class MovimientoInventarioModel extends BaseModel
{
    protected $table = 'core.movimientos_inventario';
    protected $allowedFields = ['inventario_id', 'tipo_movimiento', 'cantidad', 'motivo', 'usuario_id'];
}
