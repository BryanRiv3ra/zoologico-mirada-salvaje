<?php

namespace App\Models;

class HorarioAlimentacionModel extends BaseModel
{
    protected $table = 'alimentacion.horarios_alimentacion';
    protected $allowedFields = ['dieta_id', 'hora'];
    protected $validationRules = [
        'dieta_id' => 'required|is_natural_no_zero',
        'hora' => 'required|valid_date[H:i:s]',
    ];
}
