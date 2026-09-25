<?php

namespace App\Models;

class ZonaModel extends BaseModel
{
    protected $table = 'core.zonas';
    protected $allowedFields = ['nombre', 'tipo', 'ubicacion', 'capacidad'];
}
