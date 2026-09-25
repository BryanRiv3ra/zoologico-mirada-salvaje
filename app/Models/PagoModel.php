<?php

namespace App\Models;

class PagoModel extends BaseModel
{
    protected $table = 'entradas.pagos';
    protected $allowedFields = ['entrada_id', 'metodo', 'monto', 'estado', 'referencia'];
    protected $validationRules = [
        'entrada_id' => 'required|is_natural_no_zero',
        'metodo' => 'required|max_length[50]',
        'monto' => 'required|decimal',
        'estado' => 'permit_empty|in_list[pendiente,pagado,rechazado]',
        'referencia' => 'permit_empty|max_length[100]',
    ];
}
