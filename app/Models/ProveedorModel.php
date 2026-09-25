<?php

namespace App\Models;

class ProveedorModel extends BaseModel
{
    protected $table = 'core.proveedores';
    protected $allowedFields = ['nombre', 'contacto', 'telefono', 'email'];
    protected $validationRules = [
        'nombre' => 'required|max_length[150]',
        'contacto' => 'permit_empty|max_length[150]',
        'telefono' => 'permit_empty|max_length[20]',
        'email' => 'permit_empty|valid_email|max_length[150]',
    ];
}
