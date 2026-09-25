<?php

namespace App\Models\Core;

use CodeIgniter\Model;

/**
 * Modelo de la tabla compartida core.usuarios.
 */
class UsuarioModel extends Model
{
    protected $table         = 'core.usuarios';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['empleado_id', 'email', 'password_hash', 'activo', 'creado_en'];

    protected $validationRules = [
        'empleado_id' => 'required|is_natural_no_zero',
        'email'       => 'required|valid_email|max_length[150]',
        'activo'      => 'permit_empty|in_list[0,1]',
    ];
}