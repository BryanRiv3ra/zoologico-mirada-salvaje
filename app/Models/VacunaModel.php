<?php

namespace App\Models;

use CodeIgniter\Model;

class VacunaModel extends Model
{
    protected $table            = 'clinico.vacunas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = ['nombre', 'descripcion'];
    protected $validationRules  = [
        'nombre' => 'required|min_length[2]|max_length[100]',
    ];

    public function listaTodas(): array
    {
        return $this->orderBy('nombre', 'ASC')->findAll();
    }
}
