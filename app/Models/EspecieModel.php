<?php

namespace App\Models;

class EspecieModel extends BaseModel
{
    protected $table = 'core.especies';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nombre_comun', 'nombre_cientifico', 'descripcion'];
}
