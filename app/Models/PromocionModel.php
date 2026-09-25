<?php

namespace App\Models;

class PromocionModel extends BaseModel
{
    protected $table = 'entradas.promociones';
    protected $allowedFields = ['nombre', 'descripcion', 'descuento', 'codigo', 'fecha_inicio', 'fecha_fin'];
}
