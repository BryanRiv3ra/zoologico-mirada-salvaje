<?php

namespace App\Models;

use CodeIgniter\Model;

class AplicacionVacunaModel extends Model
{
    protected $table            = 'clinico.aplicaciones_vacunas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = ['animal_id', 'vacuna_id', 'veterinario_id', 'fecha', 'dosis'];
    protected $validationRules  = [
        'animal_id'      => 'required|is_natural_no_zero',
        'vacuna_id'      => 'required|is_natural_no_zero',
        'veterinario_id' => 'required|is_natural_no_zero',
        'fecha'          => 'required|valid_date',
    ];

    /**
     * Todas las aplicaciones con nombres legibles (join), ordenadas
     * por fecha descendente.
     */
    public function listaCompleta(): array
    {
        return $this->select('
                clinico.aplicaciones_vacunas.id,
                clinico.aplicaciones_vacunas.fecha,
                clinico.aplicaciones_vacunas.dosis,
                core.animales.nombre as animal,
                clinico.vacunas.nombre as vacuna,
                core.empleados.nombre as vet_nombre,
                core.empleados.apellido as vet_apellido
            ')
            ->join('core.animales', 'core.animales.id = clinico.aplicaciones_vacunas.animal_id')
            ->join('clinico.vacunas', 'clinico.vacunas.id = clinico.aplicaciones_vacunas.vacuna_id')
            ->join('core.empleados', 'core.empleados.id = clinico.aplicaciones_vacunas.veterinario_id')
            ->orderBy('clinico.aplicaciones_vacunas.fecha', 'DESC')
            ->findAll();
    }

    /**
     * Vacunas ya aplicadas (fecha en el pasado o presente).
     */
    public function aplicadas(): array
    {
        return $this->select('
                clinico.aplicaciones_vacunas.id,
                clinico.aplicaciones_vacunas.fecha,
                clinico.aplicaciones_vacunas.dosis,
                core.animales.nombre as animal,
                clinico.vacunas.nombre as vacuna,
                core.empleados.nombre as vet_nombre,
                core.empleados.apellido as vet_apellido
            ')
            ->join('core.animales', 'core.animales.id = clinico.aplicaciones_vacunas.animal_id')
            ->join('clinico.vacunas', 'clinico.vacunas.id = clinico.aplicaciones_vacunas.vacuna_id')
            ->join('core.empleados', 'core.empleados.id = clinico.aplicaciones_vacunas.veterinario_id')
            ->where('clinico.aplicaciones_vacunas.fecha <=', date('Y-m-d'))
            ->orderBy('clinico.aplicaciones_vacunas.fecha', 'DESC')
            ->findAll();
    }

    /**
     * Vacunas programadas a futuro (calendario de próximas vacunas).
     */
    public function proximas(): array
    {
        return $this->select('
                clinico.aplicaciones_vacunas.id,
                clinico.aplicaciones_vacunas.fecha,
                clinico.aplicaciones_vacunas.dosis,
                core.animales.nombre as animal,
                clinico.vacunas.nombre as vacuna,
                core.empleados.nombre as vet_nombre,
                core.empleados.apellido as vet_apellido
            ')
            ->join('core.animales', 'core.animales.id = clinico.aplicaciones_vacunas.animal_id')
            ->join('clinico.vacunas', 'clinico.vacunas.id = clinico.aplicaciones_vacunas.vacuna_id')
            ->join('core.empleados', 'core.empleados.id = clinico.aplicaciones_vacunas.veterinario_id')
            ->where('clinico.aplicaciones_vacunas.fecha >', date('Y-m-d'))
            ->orderBy('clinico.aplicaciones_vacunas.fecha', 'ASC')
            ->findAll();
    }

    /**
     * Verifica si ya existe una aplicación para ese animal + vacuna + fecha
     * (evita duplicados exactos, respetando el constraint único de la BD).
     */
    public function yaExiste(int $animalId, int $vacunaId, string $fecha): bool
    {
        return (bool) $this->where([
            'animal_id' => $animalId,
            'vacuna_id' => $vacunaId,
            'fecha'     => $fecha,
        ])->first();
    }
}
