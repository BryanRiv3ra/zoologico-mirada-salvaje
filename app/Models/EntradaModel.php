<?php

namespace App\Models;

class EntradaModel extends BaseModel
{
    protected $table = 'entradas.entradas';
    protected $allowedFields = ['visitante_id', 'tarifa_id', 'promocion_id', 'fecha_visita', 'codigo_qr', 'total'];
}
