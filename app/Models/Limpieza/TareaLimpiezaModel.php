<?php

namespace App\Models\Limpieza;

use CodeIgniter\Model;

/**
 * Modelo de limpieza.tareas_limpieza.
 * El campo `activo` y el UNIQUE (zona_id, descripcion) los agrega
 * database/sql/01_limpieza_cambios.sql.
 */
class TareaLimpiezaModel extends Model
{
    protected $table         = 'limpieza.tareas_limpieza';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['zona_id', 'descripcion', 'frecuencia', 'activo'];

    /**
     * PostgreSQL devuelve los booleanos como 't'/'f'; sin este cast una tarea
     * inactiva llega con 'f', que PHP evalúa como true en las vistas.
     *
     * @var list<string>
     */
    protected array $casts = ['activo' => 'boolean'];

    protected $validationRules = [
        'zona_id'    => 'required|is_natural_no_zero',
        'descripcion' => 'required|max_length[255]',
        'frecuencia' => 'permit_empty|max_length[100]',
    ];

    /**
     * Verifica que no exista la misma descripción para la misma zona.
     */
    public function existeDescripcion(int $zonaId, string $descripcion, ?int $exceptoId = null): bool
    {
        $query = $this->where('zona_id', $zonaId)->where('descripcion', $descripcion);
        if ($exceptoId !== null) {
            $query->where('id !=', $exceptoId);
        }

        return $query->countAllResults() > 0;
    }

    public function listaActivas(): array
    {
        return $this->where('activo', true)->orderBy('descripcion', 'asc')->findAll();
    }
}