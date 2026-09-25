<?php

namespace App\Models;

class PromocionModel extends BaseModel
{
    protected $table = 'entradas.promociones';
    protected $allowedFields = ['nombre', 'descripcion', 'descuento', 'codigo', 'fecha_inicio', 'fecha_fin'];
    protected $validationRules = [
        'nombre' => 'required|max_length[150]',
        'descripcion' => 'permit_empty|max_length[65535]',
        'descuento' => 'required|decimal|less_than_equal_to[100]',
        'codigo' => 'required|max_length[50]',
        'fecha_inicio' => 'required|valid_date[Y-m-d]',
        'fecha_fin' => 'required|valid_date[Y-m-d]',
    ];
}
