<?php

namespace App\Models;

class AnimalModel extends BaseModel
{
    protected $table = 'core.animales';
    protected $allowedFields = ['nombre', 'especie_id', 'zona_id', 'fecha_nacimiento', 'sexo', 'estado'];
    protected $validationRules = [
        'nombre' => 'required|max_length[100]',
        'especie_id' => 'required|is_natural_no_zero',
        'zona_id' => 'permit_empty|is_natural_no_zero',
        'fecha_nacimiento' => 'permit_empty|valid_date[Y-m-d]',
        'sexo' => 'permit_empty|in_list[M,F,N]',
        'estado' => 'permit_empty|in_list[activo,en_tratamiento,fallecido,trasladado]',
    ];
}
