<?php

namespace App\Commands;

use App\Models\Core\EmpleadoModel;
use App\Models\Core\RolModel;
use App\Models\Core\UsuarioModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Throwable;

/**
 * Crea un usuario en core.usuarios y le asigna un rol en core.usuario_rol.
 *
 * Uso: php spark usuarios:crear
 *
 * Todo se hace en una transacción: si algo falla, no queda ni el usuario ni la
 * asignación de rol a medias.
 */
class CrearUsuario extends BaseCommand
{
    protected $group       = 'Usuarios';
    protected $name        = 'usuarios:crear';
    protected $usage       = 'usuarios:crear';
    protected $description = 'Crea un usuario en core.usuarios y le asigna un rol.';

    /**
     * Mínimo exigido por CI4 en config/App.php.
     */
    private const MIN_PASSWORD = 8;

    public function run(array $params)
    {
        $db = db_connect();

        CLI::write('Crear usuario del sistema Mirada Salvaje', 'green');
        CLI::newLine();

        // ---------------------------------------------------------------
        // 1. Empleado
        // ---------------------------------------------------------------
        $empleados = $this->listarEmpleados();
        $empleadoId = CLI::prompt(
            'ID del empleado',
            $empleados === [] ? null : array_keys($empleados),
            'required|is_natural_no_zero'
        );
        $empleadoId = (int) $empleadoId;

        if (! isset($empleados[$empleadoId])) {
            CLI::error('No existe un empleado activo con el ID ' . $empleadoId . '.');
            CLI::write($this->listadoEmpleados($empleados), 'light_gray');
            CLI::newLine();

            return EXIT_ERROR;
        }

        // ---------------------------------------------------------------
        // 2. Correo
        // ---------------------------------------------------------------
        $email = CLI::prompt('Correo electrónico', null, 'required|valid_email|max_length[150]');

        $usuarios = model(UsuarioModel::class);

        if ($usuarios->existeEmail($email)) {
            CLI::error('Ya existe un usuario registrado con el correo ' . $email . '.');
            CLI::newLine();

            return EXIT_ERROR;
        }

        // ---------------------------------------------------------------
        // 3. Rol
        // ---------------------------------------------------------------
        $roles = $this->listarRoles();
        $rol   = CLI::prompt('Rol a asignar', 'administrador');
        $rol   = trim((string) $rol);

        $rolModel = model(RolModel::class);
        $filaRol  = $rolModel->where('nombre', $rol)->first();

        if ($filaRol === null) {
            CLI::error('El rol "' . $rol . '" no existe en core.roles.');
            CLI::write('Ejecuta primero database/sql/03_roles_base.sql.', 'light_gray');
            CLI::write('Roles disponibles: ' . ($roles === [] ? '(ninguno)' : implode(', ', $roles)), 'light_gray');
            CLI::newLine();

            return EXIT_ERROR;
        }

        // ---------------------------------------------------------------
        // 4. Contraseña
        // ---------------------------------------------------------------
        $password = $this->pedirPassword();

        if ($password === null) {
            return EXIT_ERROR;
        }

        // ---------------------------------------------------------------
        // 5. Alta
        // ---------------------------------------------------------------
        $db->transStart();

        try {
            // 'activo' no se envía: la columna es NOT NULL DEFAULT true.
            $usuarioId = $usuarios->insert([
                'empleado_id'   => $empleadoId,
                'email'         => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ]);

            if ($usuarioId === false) {
                throw new \RuntimeException('core.usuarios: ' . implode(' ', $usuarios->errors()));
            }

            $db->table('core.usuario_rol')->insert([
                'usuario_id' => (int) $usuarioId,
                'rol_id'     => (int) $filaRol['id'],
            ]);
        } catch (Throwable $e) {
            $db->transRollback();
            CLI::error('No se pudo crear el usuario: ' . $e->getMessage());
            CLI::newLine();

            return EXIT_ERROR;
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            CLI::error('La transacción falló y se revirtió. No se creó ningún usuario.');
            CLI::newLine();

            return EXIT_ERROR;
        }

        $nombre = $empleados[$empleadoId];

        CLI::newLine();
        CLI::write('Usuario creado correctamente.', 'green');
        CLI::write('  Empleado: ' . $nombre, 'light_gray');
        CLI::write('  Correo:   ' . $email, 'light_gray');
        CLI::write('  Rol:      ' . $rol, 'light_gray');
        CLI::newLine();

        return EXIT_SUCCESS;
    }

    /**
     * Empleados activos: id => "Nombre Apellido".
     *
     * @return array<int, string>
     */
    private function listarEmpleados(): array
    {
        $filas = model(EmpleadoModel::class)
            ->where('activo', true)
            ->orderBy('nombre', 'asc')
            ->findAll();

        $mapa = [];

        foreach ($filas as $f) {
            $mapa[(int) $f['id']] = trim($f['nombre'] . ' ' . $f['apellido']);
        }

        return $mapa;
    }

    /**
     * @return list<string>
     */
    private function listarRoles(): array
    {
        $filas = model(RolModel::class)->orderBy('nombre', 'asc')->findAll();

        return array_map(static fn (array $f): string => (string) $f['nombre'], $filas);
    }

    /**
     * @param array<int, string> $empleados
     */
    private function listadoEmpleados(array $empleados): string
    {
        if ($empleados === []) {
            return 'No hay empleados activos en core.empleados.';
        }

        $lineas = ['Empleados activos:'];

        foreach ($empleados as $id => $nombre) {
            $lineas[] = '  ' . $id . ') ' . $nombre;
        }

        return implode(PHP_EOL, $lineas);
    }

    /**
     * Pide la contraseña sin eco en pantalla.
     *
     * CI4 4.7 no trae un prompt de contraseña oculta, así que se hace a mano:
     * stty -echo en POSIX y Read-Host -AsSecureString en Windows.
     *
     * @return string|null Null si el usuario cancela o no cumple el mínimo.
     */
    private function pedirPassword(): ?string
    {
        while (true) {
            $valor = $this->leerOculto('Contraseña');

            if ($valor === null) {
                CLI::error('No se pudo leer la contraseña de forma oculta.');
                CLI::write('Cancelled.', 'light_gray');

                return null;
            }

            if (mb_strlen($valor) < self::MIN_PASSWORD) {
                CLI::error('La contraseña debe tener al menos ' . self::MIN_PASSWORD . ' caracteres.');
                CLI::newLine();

                continue;
            }

            $repetida = $this->leerOculto('Repita la contraseña');

            if ($repetida === null) {
                return null;
            }

            if (! hash_equals($valor, $repetida)) {
                CLI::error('Las contraseñas no coinciden.');
                CLI::newLine();

                continue;
            }

            return $valor;
        }
    }

    /**
     * Lee una línea sin mostrar los caracteres escritos.
     */
    private function leerOculto(string $pregunta): ?string
    {
        CLI::write($pregunta . ': ');

        if (CLI::isWindows()) {
            $script = '$s = Read-Host -AsSecureString; '
                . '$b = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($s); '
                . '[Runtime.InteropServices.Marshal]::PtrToStringBSTR($b)';

            $valor = shell_exec('powershell -NoProfile -Command "' . $script . '" 2>NUL');
        } else {
            @shell_exec('stty -echo');
            $valor = fgets(STDIN);
            @shell_exec('stty echo');
            CLI::newLine();
        }

        if (! is_string($valor)) {
            return null;
        }

        $valor = trim($valor);

        return $valor === '' ? null : $valor;
    }
}