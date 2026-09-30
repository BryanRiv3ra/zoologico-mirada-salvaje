<?php

namespace App\Models;

use CodeIgniter\Model;

class DietaModel extends Model
{
    protected $table            = 'alimentacion.dietas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'animal_id',
        'descripcion',
        'frecuencia',
    ];

    protected $validationRules = [
        'animal_id'   => 'required|is_natural_no_zero',
        'descripcion' => 'required|min_length[3]',
        'frecuencia'  => 'required|max_length[100]',
    ];

    /**
     * Lista completa de dietas con el nombre del animal.
     */
    public function listaCompleta(): array
    {
        return $this->select('
                alimentacion.dietas.id,
                alimentacion.dietas.animal_id,
                alimentacion.dietas.descripcion,
                alimentacion.dietas.frecuencia,
                core.animales.nombre as animal
            ')
            ->join('core.animales', 'core.animales.id = alimentacion.dietas.animal_id')
            ->orderBy('alimentacion.dietas.id', 'DESC')
            ->findAll();
    }
}