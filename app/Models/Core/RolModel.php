<?php

namespace App\Models\Core;

use CodeIgniter\Model;

/**
 * Modelo de la tabla compartida core.roles.
 */
class RolModel extends Model
{
    protected $table         = 'core.roles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['nombre', 'descripcion'];
}