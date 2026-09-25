<?php

namespace App\Models\Entradas;

use CodeIgniter\Model;

/**
 * Asociación promoción <-> tarifa (a qué tarifas aplica una promoción).
 */
class PromocionTarifaModel extends Model
{
    protected $table         = 'entradas.promocion_tarifa';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['promocion_id', 'tarifa_id'];

    protected $validationRules = [
        'promocion_id' => 'required|is_natural_no_zero',
        'tarifa_id'    => 'required|is_natural_no_zero',
    ];

    public function reemplazar(int $promocionId, array $tarifaIds): void
    {
        $db = db_connect();

        $db->transStart();

        $this->where('promocion_id', $promocionId)->delete();

        foreach ($tarifaIds as $tarifaId) {
            $this->insert([
                'promocion_id' => $promocionId,
                'tarifa_id'    => (int) $tarifaId,
            ]);
        }

        $db->transComplete();
    }
}