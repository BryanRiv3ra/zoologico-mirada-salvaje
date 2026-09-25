<?php

namespace App\Models;

class DietaModel extends BaseModel
{
    protected $table = 'alimentacion.dietas';
    protected $allowedFields = ['animal_id', 'descripcion', 'frecuencia'];
}
