<?php

namespace App\Models;

class EmpleadoModel extends BaseModel
{
    protected $table = 'core.empleados';
    protected $allowedFields = ['nombre', 'apellido', 'cargo', 'email', 'telefono', 'activo'];
    protected $validationRules = [
        'nombre' => 'required|max_length[100]',
        'apellido' => 'required|max_length[100]',
        'cargo' => 'permit_empty|max_length[100]',
        'email' => 'required|valid_email|max_length[150]',
        'telefono' => 'permit_empty|max_length[20]',
        'activo' => 'permit_empty|in_list[0,1]',
    ];
}
