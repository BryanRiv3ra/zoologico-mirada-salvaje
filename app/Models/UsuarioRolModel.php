<?php

namespace App\Models;

class UsuarioRolModel extends BaseModel
{
    protected $table = 'core.usuario_rol';
    protected $primaryKey = 'usuario_id';
    protected $allowedFields = ['usuario_id', 'rol_id'];
    protected $validationRules = [
        'usuario_id' => 'required|is_natural_no_zero',
        'rol_id' => 'required|is_natural_no_zero',
    ];
}
