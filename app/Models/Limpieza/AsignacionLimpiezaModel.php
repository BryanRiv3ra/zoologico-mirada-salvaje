<?php

namespace App\Models\Limpieza;

use CodeIgniter\Model;

/**
 * Modelo de limpieza.asignaciones_limpieza.
 * `asignado_por` es el usuario de la sesión (FK core.usuarios), nunca del formulario.
 */
class AsignacionLimpiezaModel extends Model
{
    protected $table         = 'limpieza.asignaciones_limpieza';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['tarea_id', 'empleado_id', 'asignado_por', 'fecha_programada', 'estado', 'inicio_real', 'fin_real'];

    protected $validationRules = [
        'tarea_id'          => 'required|is_natural_no_zero',
        'empleado_id'       => 'required|is_natural_no_zero',
        'asignado_por'      => 'required|is_natural_no_zero',
        'fecha_programada'  => 'required|valid_date[Y-m-d]',
        'estado'            => 'permit_empty|in_list[pendiente,en_curso,listo]',
    ];

    /**
     * Duplicado: la misma tarea no puede asignarse dos veces para la misma fecha.
     */
    public function tareaExisteEnFecha(int $tareaId, string $fecha): bool
    {
        return $this->where('tarea_id', $tareaId)
            ->where('fecha_programada', $fecha)
            ->countAllResults() > 0;
    }

    /**
     * Asignación con datos de tarea, zona y empleado (para listados y detalle).
     */
    public function conDetalles(int $id): ?array
    {
        $db = db_connect();

        $fila = $db->table('limpieza.asignaciones_limpieza as a')
            ->select('a.*, t.id as tarea_id, t.descripcion, t.frecuencia,
                      z.nombre as zona_nombre, z.tipo as zona_tipo, z.ubicacion as zona_ubicacion,
                      e.nombre as empleado_nombre, e.apellido as empleado_apellido')
            ->join('limpieza.tareas_limpieza as t', 't.id = a.tarea_id')
            ->join('core.zonas as z', 'z.id = t.zona_id')
            ->join('core.empleados as e', 'e.id = a.empleado_id')
            ->where('a.id', $id)
            ->get()
            ->getRowArray();

        return $fila === null ? null : $fila;
    }
}