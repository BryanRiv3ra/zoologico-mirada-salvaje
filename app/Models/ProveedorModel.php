<?php

namespace App\Models;

class ProveedorModel extends BaseModel
{
    protected $table = 'core.proveedores';
    protected $allowedFields = ['nombre', 'contacto', 'telefono', 'email'];
}
