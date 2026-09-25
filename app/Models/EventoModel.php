<?php

namespace App\Models;

class EventoModel extends BaseModel
{
    protected $table = 'entradas.eventos';
    protected $allowedFields = ['nombre', 'descripcion', 'fecha_inicio', 'fecha_fin'];
    protected $validationRules = [
        'nombre' => 'required|max_length[150]',
        'descripcion' => 'permit_empty|max_length[65535]',
        'fecha_inicio' => 'required|valid_date[Y-m-d]',
        'fecha_fin' => 'required|valid_date[Y-m-d]',
    ];
}
