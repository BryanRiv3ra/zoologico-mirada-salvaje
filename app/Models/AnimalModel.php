<?php

namespace App\Models;

use CodeIgniter\Model;

class AnimalModel extends Model
{
    protected $table            = 'core.animales';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = ['nombre', 'especie_id', 'zona_id', 'fecha_nacimiento', 'sexo', 'estado'];

    /**
     * Lista de animales activos, para llenar el <select> del formulario.
     */
    public function listaActivos(): array
    {
        return $this->where('estado', 'activo')
                    ->orderBy('nombre', 'ASC')
                    ->findAll();
    }
}
