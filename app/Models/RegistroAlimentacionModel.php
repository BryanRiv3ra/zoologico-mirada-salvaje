<?php

namespace App\Models;

class RegistroAlimentacionModel extends BaseModel
{
    protected $table = 'alimentacion.registros_alimentacion';
    protected $allowedFields = ['horario_id', 'empleado_id', 'observaciones'];
}
