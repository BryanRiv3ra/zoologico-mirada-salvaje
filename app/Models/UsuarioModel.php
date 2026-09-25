<?php

namespace App\Models;

class UsuarioModel extends BaseModel
{
    protected $table = 'core.usuarios';
    protected $allowedFields = ['empleado_id', 'email', 'password', 'activo'];
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    protected $validationRules = [
        'empleado_id' => 'required|is_natural_no_zero',
        'email' => 'required|valid_email|max_length[150]',
        'password' => 'permit_empty|min_length[12]|max_length[255]',
        'activo' => 'permit_empty|in_list[0,1]',
    ];

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password']) && $data['data']['password'] !== '') {
            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }

        unset($data['data']['password']);
        return $data;
    }
}
