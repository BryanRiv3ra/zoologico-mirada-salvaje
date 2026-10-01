<?php

namespace App\Models\Core;

use CodeIgniter\Model;

/**
 * Modelo de la tabla compartida core.empleados.
 */
class EmpleadoModel extends Model
{
    protected $table         = 'core.empleados';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['nombre', 'apellido', 'cargo', 'email', 'telefono', 'activo'];

    protected $validationRules = [
        'nombre'   => 'required|max_length[100]',
        'apellido' => 'required|max_length[100]',
        'cargo'    => 'permit_empty|max_length[100]',
        'email'    => 'required|valid_email|max_length[150]',
        'telefono' => 'permit_empty|max_length[20]',
    ];

    /**
     * Empleados activos que poseen un rol determinado (vía core.usuario_rol).
     */
    public function activosConRol(string $rol): array
    {
        $db = db_connect();

        return $db->table('core.empleados')
            ->select('core.empleados.id, core.empleados.nombre, core.empleados.apellido, core.empleados.cargo')
            ->join('core.usuarios', 'core.usuarios.empleado_id = core.empleados.id')
            ->join('core.usuario_rol', 'core.usuario_rol.usuario_id = core.usuarios.id')
            ->join('core.roles', 'core.roles.id = core.usuario_rol.rol_id')
            ->where('core.empleados.activo', true)
            ->where('core.usuarios.activo', true)
            ->where('core.roles.nombre', $rol)
            ->orderBy('core.empleados.nombre', 'asc')
            ->get()
            ->getResultArray();
    }

    public function nombreCompleto(int $id): string
    {
        $empleado = $this->find($id);

        return $empleado === null ? '' : trim($empleado['nombre'] . ' ' . ($empleado['apellido'] ?? ''));
    }
}