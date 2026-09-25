<?php

namespace App\Models;

class EmpleadoModel extends BaseModel
{
    protected $table = 'core.empleados';
    protected $allowedFields = ['nombre', 'apellido', 'cargo', 'email', 'telefono', 'activo'];
}
