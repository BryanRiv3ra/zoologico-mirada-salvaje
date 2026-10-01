<?php

namespace App\Models;

use CodeIgniter\Model;

class MovimientoInventarioModel extends Model
{
    protected $table            = 'core.movimientos_inventario';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = [
        'inventario_id', 'tipo_movimiento', 'cantidad', 'fecha', 'motivo', 'usuario_id',
    ];
}
