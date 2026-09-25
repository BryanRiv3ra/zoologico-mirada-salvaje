<?php

namespace App\Models;

class HistorialClinicoModel extends BaseModel
{
    protected $table = 'clinico.historial_clinico';
    protected $allowedFields = ['animal_id', 'diagnostico', 'veterinario_id'];
    protected $validationRules = [
        'animal_id' => 'required|is_natural_no_zero',
        'diagnostico' => 'required|max_length[65535]',
        'veterinario_id' => 'required|is_natural_no_zero',
    ];
}
