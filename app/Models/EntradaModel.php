<?php

namespace App\Models;

class EntradaModel extends BaseModel
{
    protected $table = 'entradas.entradas';
    protected $allowedFields = ['visitante_id', 'tarifa_id', 'promocion_id', 'fecha_visita', 'codigo_qr', 'total'];
    protected $validationRules = [
        'visitante_id' => 'required|is_natural_no_zero',
        'tarifa_id' => 'required|is_natural_no_zero',
        'promocion_id' => 'permit_empty|is_natural_no_zero',
        'fecha_visita' => 'required|valid_date[Y-m-d]',
        'codigo_qr' => 'required|max_length[100]',
        'total' => 'required|decimal',
    ];
}
