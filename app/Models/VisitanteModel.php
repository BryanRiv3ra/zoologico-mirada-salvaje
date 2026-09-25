<?php

namespace App\Models;

class VisitanteModel extends BaseModel
{
    protected $table = 'entradas.visitantes';
    protected $allowedFields = ['nombre', 'email', 'telefono'];
    protected $validationRules = [
        'nombre' => 'required|max_length[150]',
        'email' => 'permit_empty|valid_email|max_length[150]',
        'telefono' => 'permit_empty|max_length[20]',
    ];
}
