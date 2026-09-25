<?php

namespace App\Models;

class RegistroAlimentacionModel extends BaseModel
{
    protected $table = 'alimentacion.registros_alimentacion';
    protected $allowedFields = ['horario_id', 'empleado_id', 'observaciones'];
    protected $validationRules = [
        'horario_id' => 'required|is_natural_no_zero',
        'empleado_id' => 'required|is_natural_no_zero',
        'observaciones' => 'permit_empty|max_length[65535]',
    ];
}
