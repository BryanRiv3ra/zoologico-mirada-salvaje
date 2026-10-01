<?php

namespace App\Models;

use CodeIgniter\Model;

class RegistroAlimentacionModel extends Model
{
    protected $table            = 'alimentacion.registros_alimentacion';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'horario_id',
        'empleado_id',
        'fecha',
        'observaciones',
    ];

    protected $validationRules = [
        'horario_id'  => 'required|is_natural_no_zero',
        'empleado_id' => 'required|is_natural_no_zero',
    ];

    /**
     * Lista los registros de alimentación con horario, dieta, animal y empleado.
     */
    public function listaCompleta(): array
    {
        return $this->select('
                alimentacion.registros_alimentacion.id,
                alimentacion.registros_alimentacion.horario_id,
                alimentacion.registros_alimentacion.empleado_id,
                alimentacion.registros_alimentacion.fecha,
                alimentacion.registros_alimentacion.observaciones,
                alimentacion.horarios_alimentacion.hora,
                alimentacion.dietas.descripcion,
                core.animales.nombre as animal,
                core.empleados.nombre as empleado_nombre,
                core.empleados.apellido as empleado_apellido
            ')
            ->join('alimentacion.horarios_alimentacion', 'alimentacion.horarios_alimentacion.id = alimentacion.registros_alimentacion.horario_id')
            ->join('alimentacion.dietas', 'alimentacion.dietas.id = alimentacion.horarios_alimentacion.dieta_id')
            ->join('core.animales', 'core.animales.id = alimentacion.dietas.animal_id')
            ->join('core.empleados', 'core.empleados.id = alimentacion.registros_alimentacion.empleado_id')
            ->orderBy('alimentacion.registros_alimentacion.fecha', 'DESC')
            ->findAll();
    }
}