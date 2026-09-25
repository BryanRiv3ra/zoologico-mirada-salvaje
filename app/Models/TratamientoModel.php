<?php

namespace App\Models;

class TratamientoModel extends BaseModel
{
    protected $table = 'clinico.tratamientos';
    protected $allowedFields = ['historial_id', 'inventario_id', 'dosis', 'frecuencia', 'fecha_inicio', 'fecha_fin'];
}
