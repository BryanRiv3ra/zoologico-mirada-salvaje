<?php

namespace App\Models\Core;

use CodeIgniter\Model;

/**
 * Modelo de la tabla compartida core.usuarios.
 *
 * Estructura real (verificada en information_schema):
 *   id            serial      PK
 *   empleado_id   int         NOT NULL  FK core.empleados(id)
 *   email         varchar(150) NOT NULL  UNIQUE
 *   password_hash varchar(255) NOT NULL
 *   activo        boolean     NOT NULL  DEFAULT true
 *   creado_en     timestamp   NOT NULL  DEFAULT now()
 *
 * No hay columna `nombre`: el nombre del usuario se arma con core.empleados.
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

    /**
     * Busca un usuario por correo junto con los datos de su empleado, exigiendo
     * que tanto el usuario como el empleado estén activos.
     *
     * Devuelve null si el correo no existe, si el usuario está desactivado o si
     * su empleado está desactivado. Se usa en el login: los tres casos deben
     * producir exactamente el mismo resultado para no filtrar información.
     *
     * @return array{id:int, empleado_id:int, email:string, password_hash:string,
     *               nombre:string, apellido:string}|null
     */
    public function buscarActivoConEmpleado(string $email): ?array
    {
        $db = db_connect();

        return $db->table('core.usuarios as u')
            ->select('u.id, u.empleado_id, u.email, u.password_hash, e.nombre, e.apellido')
            ->join('core.empleados as e', 'e.id = u.empleado_id')
            ->where('u.email', $email)
            ->where('u.activo', true)
            ->where('e.activo', true)
            ->get()
            ->getRowArray();
    }

    /**
     * Nombres de los roles asignados a un usuario, vía core.usuario_rol.
     *
     * @return list<string>
     */
    public function rolesDe(int $usuarioId): array
    {
        $db = db_connect();

        $filas = $db->table('core.usuario_rol as ur')
            ->select('r.nombre')
            ->join('core.roles as r', 'r.id = ur.rol_id')
            ->where('ur.usuario_id', $usuarioId)
            ->orderBy('r.nombre', 'asc')
            ->get()
            ->getResultArray();

        return array_map(static fn (array $f): string => (string) $f['nombre'], $filas);
    }

    /**
     * Un usuario puede tener como mucho un registro por correo (UNIQUE
     * usuarios_email_key en la BD), pero se pregunta explícitamente para poder
     * dar un mensaje claro desde el comando de consola.
     */
    public function existeEmail(string $email, ?int $exceptoId = null): bool
    {
        $query = $this->where('email', $email);

        if ($exceptoId !== null) {
            $query->where('id !=', $exceptoId);
        }

        return $query->countAllResults() > 0;
    }
}
