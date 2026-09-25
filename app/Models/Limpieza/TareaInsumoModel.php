<?php

namespace App\Models\Limpieza;

use CodeIgniter\Model;

/**
 * Modelo de limpieza.tarea_insumo (relación tarea <-> insumo de inventario).
 */
class TareaInsumoModel extends Model
{
    protected $table         = 'limpieza.tarea_insumo';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['tarea_id', 'inventario_id', 'cantidad'];

    protected $validationRules = [
        'tarea_id'      => 'required|is_natural_no_zero',
        'inventario_id' => 'required|is_natural_no_zero',
        'cantidad'      => 'required|greater_than[0]',
    ];

    /**
     * Inserta un insumo a una tarea sin duplicar el mismo inventario.
     */
    public function agregar(int $tareaId, int $inventarioId, float $cantidad): bool
    {
        if (cantidad <= 0) {
            return false;
        }
        if ($this->existeInsumo($tareaId, $inventarioId)) {
            return false;
        }

        return $this->insert([
            'tarea_id'      => $tareaId,
            'inventario_id' => $inventarioId,
            'cantidad'      => $cantidad,
        ]) !== false;
    }

    public function existeInsumo(int $tareaId, int $inventarioId): bool
    {
        return $this->where('tarea_id', $tareaId)
            ->where('inventario_id', $inventarioId)
            ->countAllResults() > 0;
    }

    /**
     * Insumos de una tarea unidos con core.inventario (stock de referencia).
     */
    public function deTarea(int $tareaId): array
    {
        $db = db_connect();

        return $db->table('limpieza.tarea_insumo')
            ->select('limpieza.tarea_insumo.id, limpieza.tarea_insumo.cantidad, core.inventario.nombre,
                      core.inventario.unidad, core.inventario.stock_actual')
            ->join('core.inventario', 'core.inventario.id = limpieza.tarea_insumo.inventario_id')
            ->where('limpieza.tarea_insumo.tarea_id', $tareaId)
            ->orderBy('core.inventario.nombre', 'asc')
            ->get()
            ->getResultArray();
    }
}