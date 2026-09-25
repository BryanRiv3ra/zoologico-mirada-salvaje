<?php

namespace App\Models;

class TarifaModel extends BaseModel
{
    protected $table = 'entradas.tarifas';
    protected $allowedFields = ['nombre', 'tipo_visitante', 'precio'];
    protected $validationRules = [
        'nombre' => 'required|max_length[100]',
        'tipo_visitante' => 'required|max_length[50]',
        'precio' => 'required|decimal',
    ];
}
