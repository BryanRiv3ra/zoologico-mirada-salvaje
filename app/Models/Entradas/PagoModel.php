<?php

namespace App\Models\Entradas;

use CodeIgniter\Model;

class PagoModel extends Model
{
    protected $table         = 'entradas.pagos';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['entrada_id', 'metodo', 'monto', 'fecha', 'estado', 'referencia'];

    protected $validationRules = [
        'entrada_id' => 'required|is_natural_no_zero',
        'metodo'     => 'required|max_length[50]',
        'monto'      => 'required|decimal|greater_than[0]',
        'estado'     => 'required|in_list[pendiente,pagado,rechazado]',
    ];
}