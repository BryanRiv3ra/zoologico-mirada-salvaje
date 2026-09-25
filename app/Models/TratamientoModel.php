<?php

namespace App\Models;

class TratamientoModel extends BaseModel
{
    protected $table = 'clinico.tratamientos';
    protected $allowedFields = ['historial_id', 'inventario_id', 'dosis', 'frecuencia', 'fecha_inicio', 'fecha_fin'];
    protected $validationRules = [
        'historial_id' => 'required|is_natural_no_zero',
        'inventario_id' => 'required|is_natural_no_zero',
        'dosis' => 'required|max_length[100]',
        'frecuencia' => 'permit_empty|max_length[100]',
        'fecha_inicio' => 'required|valid_date[Y-m-d]',
        'fecha_fin' => 'permit_empty|valid_date[Y-m-d]',
    ];
}
