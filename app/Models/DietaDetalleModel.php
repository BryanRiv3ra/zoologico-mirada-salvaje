<?php

namespace App\Models;

class DietaDetalleModel extends BaseModel
{
    protected $table = 'alimentacion.dieta_detalle';
    protected $allowedFields = ['dieta_id', 'inventario_id', 'cantidad'];
    protected $validationRules = [
        'dieta_id' => 'required|is_natural_no_zero',
        'inventario_id' => 'required|is_natural_no_zero',
        'cantidad' => 'required|decimal',
    ];
}
