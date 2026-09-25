<?php

namespace App\Models;

class UsuarioModel extends BaseModel
{
    protected $table = 'core.usuarios';
    protected $allowedFields = ['empleado_id', 'email', 'password_hash', 'activo'];
}
