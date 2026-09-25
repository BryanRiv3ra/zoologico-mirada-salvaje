<?php

namespace App\Models;

class TarifaModel extends BaseModel
{
    protected $table = 'entradas.tarifas';
    protected $allowedFields = ['nombre', 'tipo_visitante', 'precio'];
}
