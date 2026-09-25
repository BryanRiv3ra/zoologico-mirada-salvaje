<?php

namespace App\Models\Entradas;

use CodeIgniter\Model;

/**
 * Catálogo de tarifas de ingreso.
 */
class TarifaModel extends Model
{
    protected $table         = 'entradas.tarifas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['nombre', 'tipo_visitante', 'precio', 'activo'];

    protected $validationRules = [
        'nombre'        => 'required|max_length[100]',
        'tipo_visitante' => 'required|max_length[50]',
        'precio'        => 'required|decimal|greater_than[0]',
    ];

    protected $validationMessages = [
        'precio' => [
            'greater_than' => 'El precio debe ser mayor a cero.',
        ],
    ];

    public function activas(): array
    {
        return $this->where('activo', true)
            ->orderBy('nombre', 'asc')
            ->findAll();
    }

    public function todas(): array
    {
        return $this->orderBy('activo', 'desc')
            ->orderBy('nombre', 'asc')
            ->findAll();
    }
}