<?php

namespace App\Models;

class TareaLimpiezaModel extends BaseModel
{
    protected $table = 'limpieza.tareas_limpieza';
    protected $allowedFields = ['zona_id', 'descripcion', 'frecuencia'];
    protected $validationRules = [
        'zona_id' => 'required|is_natural_no_zero',
        'descripcion' => 'required|max_length[255]',
        'frecuencia' => 'permit_empty|max_length[100]',
    ];
}
