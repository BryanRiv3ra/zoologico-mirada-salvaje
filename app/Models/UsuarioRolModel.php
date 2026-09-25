<?php

namespace App\Models;

class UsuarioRolModel extends BaseModel
{
    protected $table = 'core.usuario_rol';
    protected $primaryKey = 'usuario_id';
    protected $allowedFields = ['usuario_id', 'rol_id'];
}
