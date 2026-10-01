<?php

namespace App\Models\Entradas;

use CodeIgniter\Model;

/**
 * Visitantes/clientes que compran entradas (portal o taquilla).
 */
class VisitanteModel extends Model
{
    protected $table         = 'entradas.visitantes';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['nombre', 'email', 'telefono'];

    protected $validationRules = [
        'nombre'  => 'required|max_length[150]',
        'email'   => 'permit_empty|valid_email|max_length[150]',
        'telefono' => 'permit_empty|max_length[20]',
    ];

    public function buscarPorEmail(string $email): ?array
    {
        if ($email === '') {
            return null;
        }

        return $this->where('email', $email)->first();
    }
}