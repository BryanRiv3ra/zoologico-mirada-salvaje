<?php

namespace App\Models;

class VisitanteModel extends BaseModel
{
    protected $table = 'entradas.visitantes';
    protected $allowedFields = ['nombre', 'email', 'telefono'];
}
