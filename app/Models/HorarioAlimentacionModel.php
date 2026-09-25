<?php

namespace App\Models;

class HorarioAlimentacionModel extends BaseModel
{
    protected $table = 'alimentacion.horarios_alimentacion';
    protected $allowedFields = ['dieta_id', 'hora'];
}
