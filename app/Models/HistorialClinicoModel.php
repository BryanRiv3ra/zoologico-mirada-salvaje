<?php

namespace App\Models;

use CodeIgniter\Model;

class HistorialClinicoModel extends Model
{
    protected $table            = 'clinico.historial_clinico';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = ['animal_id', 'fecha', 'diagnostico', 'veterinario_id'];
    protected $validationRules  = [
        'animal_id'      => 'required|is_natural_no_zero',
        'diagnostico'    => 'required|min_length[3]',
        'veterinario_id' => 'required|is_natural_no_zero',
    ];
}
