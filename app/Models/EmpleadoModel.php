<?php

namespace App\Models;

use CodeIgniter\Model;

class EmpleadoModel extends Model
{
    protected $table            = 'core.empleados';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = ['nombre', 'apellido', 'cargo', 'email', 'telefono', 'activo'];

    /**
     * Empleados activos, para llenar el <select> de veterinario responsable.
     */
    public function listaActivos(): array
    {
        return $this->where('activo', true)
                    ->orderBy('nombre', 'ASC')
                    ->findAll();
    }
}
