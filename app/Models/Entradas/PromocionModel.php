<?php

namespace App\Models\Entradas;

use CodeIgniter\Model;

/**
 * Promociones aplicables a boletos (descuento porcentual).
 * Adaptado al ER real: no existe tabla de relación promocion_tarifa,
 * así que toda promoción vigente aplica a todas las tarifas activas.
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
}