<?php

namespace App\Models;

use CodeIgniter\Model;

class TratamientoModel extends Model
{
    protected $table            = 'clinico.tratamientos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = [
        'historial_id', 'inventario_id', 'dosis', 'frecuencia',
        'fecha_inicio', 'fecha_fin',
    ];
    protected $validationRules  = [
        'historial_id'  => 'required|is_natural_no_zero',
        'inventario_id' => 'required|is_natural_no_zero',
        'dosis'         => 'required|min_length[1]|max_length[100]',
        'fecha_inicio'  => 'required|valid_date',
    ];

    /**
     * Lista completa para la tabla del módulo: junta tratamiento + el
     * diagnóstico del que depende + el animal + el insumo recetado.
     * Todo en una sola consulta para no hacer N+1 queries.
     */
    public function listaCompleta(): array
    {
        return $this->select('
                clinico.tratamientos.id,
                clinico.tratamientos.dosis,
                clinico.tratamientos.frecuencia,
                clinico.tratamientos.fecha_inicio,
                clinico.tratamientos.fecha_fin,
                clinico.historial_clinico.diagnostico,
                core.animales.nombre as animal,
                core.inventario.nombre as insumo,
                core.inventario.tipo as tipo_insumo
            ')
            ->join('clinico.historial_clinico', 'clinico.historial_clinico.id = clinico.tratamientos.historial_id')
            ->join('core.animales', 'core.animales.id = clinico.historial_clinico.animal_id')
            ->join('core.inventario', 'core.inventario.id = clinico.tratamientos.inventario_id')
            ->orderBy('clinico.tratamientos.id', 'DESC')
            ->findAll();
    }
}
