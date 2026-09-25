<?php

namespace App\Models;

class HistorialClinicoModel extends BaseModel
{
    protected $table = 'clinico.historial_clinico';
    protected $allowedFields = ['animal_id', 'diagnostico', 'veterinario_id'];
}
