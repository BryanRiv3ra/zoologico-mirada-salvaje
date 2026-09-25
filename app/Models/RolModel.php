<?php

namespace App\Models;

class RolModel extends BaseModel
{
    protected $table = 'core.roles';
    protected $allowedFields = ['nombre', 'descripcion'];
    protected $validationRules = [
        'nombre' => 'required|max_length[50]',
        'descripcion' => 'permit_empty|max_length[255]',
    ];
}
