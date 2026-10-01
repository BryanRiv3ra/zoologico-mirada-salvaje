<?php

namespace App\Models\Limpieza;

use CodeIgniter\Model;

/**
 * Modelo de limpieza.registros_limpieza.
 * Registra la ejecución real de una asignación (se crea al finalizar).
 */
class RegistroLimpiezaModel extends Model
{
    protected $table         = 'limpieza.registros_limpieza';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['asignacion_id', 'tarea_id', 'empleado_id', 'fecha', 'hora_inicio', 'hora_fin', 'observaciones'];

    protected $validationRules = [
        'asignacion_id' => 'required|is_natural_no_zero',
        'tarea_id'      => 'required|is_natural_no_zero',
        'empleado_id'   => 'required|is_natural_no_zero',
        'fecha'         => 'required|valid_date[Y-m-d]',
        'observaciones' => 'permit_empty|max_length[500]',
    ];
}