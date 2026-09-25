<?php

namespace App\Models;

class VacunaModel extends BaseModel
{
    protected $table = 'clinico.vacunas';
    protected $allowedFields = ['nombre', 'descripcion'];
}
