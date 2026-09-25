<?php

namespace App\Models;

class VacunaModel extends BaseModel
{
    protected $table = 'clinico.vacunas';
    protected $allowedFields = ['nombre', 'descripcion'];
    protected $validationRules = [
        'nombre' => 'required|max_length[100]',
        'descripcion' => 'permit_empty|max_length[65535]',
    ];
}
