<?php

namespace App\Models;

class RegistroLimpiezaModel extends BaseModel
{
    protected $table = 'limpieza.registros_limpieza';
    protected $allowedFields = ['tarea_id', 'empleado_id', 'hora_inicio', 'hora_fin', 'observaciones'];
}
