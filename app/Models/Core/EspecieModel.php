<?php

namespace App\Models\Core;

use CodeIgniter\Model;

/**
 * Modelo de la tabla compartida core.especies.
 *
 * Estructura real (verificada en information_schema):
 *   id                 serial      PK
 *   nombre_comun       varchar(100) NOT NULL  UNIQUE (especies_nombre_comun_key)
 *   nombre_cientifico  varchar(150) NULL       UNIQUE (especies_nombre_cientifico_key)
 *   descripcion        text        NULL
 *   creado_en          timestamp   NOT NULL  DEFAULT now()
 *
 * Ambas columnas de nombre son UNIQUE en la BD. El UNIQUE de PostgreSQL es
 * case-sensitive, así que "León" y "león" se consideran distintas; la
 * comprobación de duplicados que hace el controlador es insensible a mayúsculas
 * para que el usuario no se sorprenda.
 */
class EspecieModel extends Model
{
    protected $table         = 'core.especies';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['nombre_comun', 'nombre_cientifico', 'descripcion'];

    protected $validationRules = [
        'nombre_comun'      => 'required|max_length[100]',
        'nombre_cientifico' => 'permit_empty|max_length[150]',
        'descripcion'       => 'permit_empty',
    ];

    /**
     * Todas las especies, en orden alfabético, para el listado y los <select>.
     *
     * @return list<array<string, mixed>>
     */
    public function listaCompleta(): array
    {
        return $this->orderBy('nombre_comun', 'asc')->findAll();
    }

    /**
     * ¿Existe ya una especie con ese nombre común?
     *
     * La comparación es case-insensitive (LOWER) para que coincida con lo que
     * el usuario espera, aunque el UNIQUE de la BD distinga mayúsculas.
     */
    public function existeNombreComun(string $nombre, ?int $exceptoId = null): bool
    {
        $query = $this->builder()
            ->where('LOWER(nombre_comun)', mb_strtolower($nombre));

        if ($exceptoId !== null) {
            $query->where('id !=', $exceptoId);
        }

        return $query->countAllResults() > 0;
    }

    /**
     * ¿Existe ya una especie con ese nombre científico?
     *
     * El UNIQUE de PostgreSQL SÍ distingue mayúsculas, pero en nombres
     * científicos la diferencia suele ser un error de dedo, no una especie
     * distinta, así que también se compara con LOWER.
     */
    public function existeNombreCientifico(string $nombre, ?int $exceptoId = null): bool
    {
        $query = $this->builder()
            ->where('nombre_cientifico IS NOT NULL')
            ->where('LOWER(nombre_cientifico)', mb_strtolower($nombre));

        if ($exceptoId !== null) {
            $query->where('id !=', $exceptoId);
        }

        return $query->countAllResults() > 0;
    }

    /**
     * Número de animales que tienen asignada esta especie.
     *
     * Lo usa el controlador para no borrar una especie en uso.
     */
    public function contarAnimales(int $especieId): int
    {
        return $this->db->table('core.animales')
            ->where('especie_id', $especieId)
            ->countAllResults();
    }
}
