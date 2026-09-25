<?php

namespace App\Models;

class EspecieModel extends BaseModel
{
    protected $table = 'core.especies';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nombre_comun', 'nombre_cientifico', 'descripcion'];
    protected $validationRules = [
        'nombre_comun' => 'required|max_length[100]',
        'nombre_cientifico' => 'permit_empty|max_length[150]',
        'descripcion' => 'permit_empty|max_length[65535]',
    ];
}
