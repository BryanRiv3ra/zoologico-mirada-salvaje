<?php

namespace App\Models;

class TareaLimpiezaModel extends BaseModel
{
    protected $table = 'limpieza.tareas_limpieza';
    protected $allowedFields = ['zona_id', 'descripcion', 'frecuencia'];
}
