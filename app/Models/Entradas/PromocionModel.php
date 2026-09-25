<?php

namespace App\Models\Entradas;

use CodeIgniter\Model;

/**
 * Promociones aplicables a tarifas (descuento porcentual).
 */
class PromocionModel extends Model
{
    protected $table         = 'entradas.promociones';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'nombre', 'descripcion', 'descuento', 'codigo', 'fecha_inicio', 'fecha_fin', 'activo',
    ];

    protected $validationRules = [
        'nombre'       => 'required|max_length[150]',
        'descripcion'  => 'permit_empty|max_length[65535]',
        'descuento'    => 'required|decimal|greater_than[0]|less_than_equal_to[100]',
        'codigo'       => 'required|alpha_numeric|max_length[50]',
        'fecha_inicio' => 'required|valid_date[Y-m-d]',
        'fecha_fin'    => 'required|valid_date[Y-m-d]',
    ];

    protected $validationMessages = [
        'descuento' => [
            'greater_than'       => 'El descuento debe ser mayor a cero.',
            'less_than_equal_to' => 'El descuento no puede superar el 100%.',
        ],
        'codigo' => [
            'alpha_numeric' => 'El código de promoción solo puede contener letras y números.',
        ],
    ];

    public function activas(): array
    {
        $hoy = date('Y-m-d');

        return $this->where('activo', true)
            ->where('fecha_inicio <=', $hoy)
            ->where('fecha_fin >=', $hoy)
            ->orderBy('nombre', 'asc')
            ->findAll();
    }

    public function todas(): array
    {
        return $this->orderBy('activo', 'desc')
            ->orderBy('nombre', 'asc')
            ->findAll();
    }

    /**
     * Promoción con sus tarifas asociadas.
     */
    public function conTarifas(int $id): ?array
    {
        $promocion = $this->find($id);
        if ($promocion === null) {
            return null;
        }

        $db = db_connect();
        $promocion['tarifas'] = $db->table('entradas.promocion_tarifa as pt')
            ->select('t.id, t.nombre, t.precio, t.tipo_visitante')
            ->join('entradas.tarifas as t', 't.id = pt.tarifa_id')
            ->where('pt.promocion_id', $id)
            ->orderBy('t.nombre', 'asc')
            ->get()
            ->getResultArray();

        return $promocion;
    }
}