<?php

namespace App\Models;

class RolModel extends BaseModel
{
    protected $table = 'core.roles';
    protected $allowedFields = ['nombre', 'descripcion'];
}
