<?php

namespace App\Models;

class RegistroLimpiezaModel extends BaseModel
{
    protected $table = 'limpieza.registros_limpieza';
    protected $allowedFields = ['tarea_id', 'empleado_id', 'hora_inicio', 'hora_fin', 'observaciones'];
    protected $validationRules = [
        'tarea_id' => 'required|is_natural_no_zero',
        'empleado_id' => 'required|is_natural_no_zero',
        'hora_inicio' => 'permit_empty|valid_date[H:i:s]',
        'hora_fin' => 'permit_empty|valid_date[H:i:s]',
        'observaciones' => 'permit_empty|max_length[65535]',
    ];
}
