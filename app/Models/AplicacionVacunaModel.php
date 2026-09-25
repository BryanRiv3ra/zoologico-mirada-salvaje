<?php

namespace App\Models;

class AplicacionVacunaModel extends BaseModel
{
    protected $table = 'clinico.aplicaciones_vacunas';
    protected $allowedFields = ['animal_id', 'vacuna_id', 'veterinario_id', 'fecha', 'dosis'];
    protected $validationRules = [
        'animal_id' => 'required|is_natural_no_zero',
        'vacuna_id' => 'required|is_natural_no_zero',
        'veterinario_id' => 'required|is_natural_no_zero',
        'fecha' => 'required|valid_date[Y-m-d]',
        'dosis' => 'permit_empty|max_length[50]',
    ];
}
