<?php

namespace App\Models;

class PagoModel extends BaseModel
{
    protected $table = 'entradas.pagos';
    protected $allowedFields = ['entrada_id', 'metodo', 'monto', 'estado', 'referencia'];
}
