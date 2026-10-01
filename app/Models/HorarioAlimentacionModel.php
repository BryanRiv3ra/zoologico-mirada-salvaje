<?php

namespace App\Models;

use CodeIgniter\Model;

class HorarioAlimentacionModel extends Model
{
    protected $table            = 'alimentacion.horarios_alimentacion';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'dieta_id',
        'hora',
    ];

    protected $validationRules = [
        'dieta_id' => 'required|is_natural_no_zero',
        'hora'     => 'required',
    ];

    /**
     * Lista los horarios junto con la dieta y el animal correspondiente.
     */
    public function listaCompleta(): array
    {
        return $this->select('
                alimentacion.horarios_alimentacion.id,
                alimentacion.horarios_alimentacion.dieta_id,
                alimentacion.horarios_alimentacion.hora,
                alimentacion.dietas.descripcion,
                core.animales.nombre as animal
            ')
            ->join('alimentacion.dietas', 'alimentacion.dietas.id = alimentacion.horarios_alimentacion.dieta_id')
            ->join('core.animales', 'core.animales.id = alimentacion.dietas.animal_id')
            ->orderBy('alimentacion.horarios_alimentacion.hora', 'ASC')
            ->findAll();
    }
}