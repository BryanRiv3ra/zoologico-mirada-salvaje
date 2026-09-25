<?php

namespace App\Models;

class ZonaModel extends BaseModel
{
    protected $table = 'core.zonas';
    protected $allowedFields = ['nombre', 'tipo', 'ubicacion', 'capacidad'];
    protected $validationRules = [
        'nombre' => 'required|max_length[100]',
        'tipo' => 'required|in_list[jaula,sanitario,jardin,area_juegos,oficina]',
        'ubicacion' => 'permit_empty|max_length[150]',
        'capacidad' => 'permit_empty|is_natural',
    ];
}
