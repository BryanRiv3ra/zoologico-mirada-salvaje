<?php

namespace App\Models;

class EventoModel extends BaseModel
{
    protected $table = 'entradas.eventos';
    protected $allowedFields = ['nombre', 'descripcion', 'fecha_inicio', 'fecha_fin'];
}
