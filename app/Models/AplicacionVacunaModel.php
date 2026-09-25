<?php

namespace App\Models;

class AplicacionVacunaModel extends BaseModel
{
    protected $table = 'clinico.aplicaciones_vacunas';
    protected $allowedFields = ['animal_id', 'vacuna_id', 'veterinario_id', 'fecha', 'dosis'];
}
