<?php

namespace App\Models;

class DietaModel extends BaseModel
{
    protected $table = 'alimentacion.dietas';
    protected $allowedFields = ['animal_id', 'descripcion', 'frecuencia'];
    protected $validationRules = [
        'animal_id' => 'required|is_natural_no_zero',
        'descripcion' => 'required|max_length[255]',
        'frecuencia' => 'permit_empty|max_length[100]',
    ];
}
