<?php

namespace App\Models;

class DietaDetalleModel extends BaseModel
{
    protected $table = 'alimentacion.dieta_detalle';
    protected $allowedFields = ['dieta_id', 'inventario_id', 'cantidad'];
}
