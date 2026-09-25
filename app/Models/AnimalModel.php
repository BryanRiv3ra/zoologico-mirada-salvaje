<?php

namespace App\Models;

class AnimalModel extends BaseModel
{
    protected $table = 'core.animales';
    protected $allowedFields = ['nombre', 'especie_id', 'zona_id', 'fecha_nacimiento', 'sexo', 'estado'];
}
