<?php

namespace App\Models\Core;

use CodeIgniter\Model;

/**
 * Modelo de la tabla compartida core.zonas.
 * Toca tabla compartida (Alimentación la usa vía animales.zona_id): no cambiar
 * su estructura sin aprobación.
 */
class ZonaModel extends Model
{
    protected $table         = 'core.zonas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['nombre', 'tipo', 'ubicacion', 'capacidad', 'activo'];

    protected $validationRules = [
        'nombre'    => 'required|max_length[100]',
        'tipo'      => 'required|in_list[jaula,sanitario,jardin,area_juegos,oficina]',
        'ubicacion' => 'permit_empty|max_length[150]',
        'capacidad' => 'permit_empty|is_natural',
    ];

    /**
     * Verifica que el nombre de zona no esté duplicado (único).
     */
    public function existeNombre(string $nombre, ?int $exceptoId = null): bool
    {
        $query = $this->where('nombre', $nombre);
        if ($exceptoId !== null) {
            $query->where('id !=', $exceptoId);
        }

        return $query->countAllResults() > 0;
    }

    public function listaActivas(): array
    {
        return $this->where('activo', true)->orderBy('nombre', 'asc')->findAll();
    }
}