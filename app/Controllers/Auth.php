<?php

namespace App\Controllers;

use App\Models\Core\UsuarioModel;

/**
 * Inicio y cierre de sesión.
 *
 * Contrato de sesión (lo consumen AuthFilter, RolFilter y Dev.php):
 *   usuario_id  int       core.usuarios.id
 *   empleado_id int       core.usuarios.empleado_id
 *   nombre      string    nombre + apellido de core.empleados
 *   email       string    core.usuarios.email
 *   roles       string[]  core.roles.nombre vía core.usuario_rol
 */
class Auth extends BaseController
{
    protected $helpers = ['url', 'form'];

    private const ERROR_GENERICO = 'Correo o contraseña incorrectos.';

    /**
     * GET /login. Si ya hay sesión activa, no tiene sentido mostrar el formulario.
     */
    public function index()
    {
        if (session('usuario_id') !== null) {
            return redirect()->to('/');
        }

        return view('auth/login', ['titulo' => 'Iniciar sesión']);
    }

    /**
     * POST /login.
     */
    public function autenticar()
    {
        $reglas = [
            'email'    => 'required|valid_email|max_length[150]',
            'password' => 'required|max_length[255]',
        ];

        if (! $this->validate($reglas)) {
            return redirect()
                ->to('/login')
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email    = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        $usuarios = model(UsuarioModel::class);
        $fila     = $usuarios->buscarActivoConEmpleado($email);

        // Si el correo no existe se hace igualmente una verificación contra un
        // hash ficticio: así el tiempo de respuesta no revela qué correos están
        // registrados (enumeración de usuarios por temporización).
        $hash = $fila['password_hash'] ?? '$2y$10$usuario.inexistente.no.puede.existir.na';

        if (! password_verify($password, $hash) || $fila === null) {
            return redirect()->to('/login')->withInput()->with('error', self::ERROR_GENERICO);
        }

        $roles = $usuarios->rolesDe((int) $fila['id']);

        // Sin roles no hay a qué módulo dar acceso: se rechaza el ingreso en
        // lugar de dejar una sesión que solo mostraría la vista de prohibido.
        if ($roles === []) {
            return redirect()->to('/login')->withInput()->with('error', self::ERROR_GENERICO);
        }

        // Nueva ID de sesión para evitar el secuestro de sesión previo.
        session()->regenerate();

        session()->set([
            'usuario_id'  => (int) $fila['id'],
            'empleado_id' => (int) $fila['empleado_id'],
            'nombre'      => trim($fila['nombre'] . ' ' . $fila['apellido']),
            'email'       => $fila['email'],
            'roles'       => $roles,
        ]);

        $destino = $this->destinoPendiente();

        return redirect()->to($destino)
            ->with('success', 'Sesión iniciada. Bienvenido, ' . session('nombre') . '.');
    }

    /**
     * GET|POST /logout.
     *
     * POST existe porque app/Views/errors/prohibido.php ya envía un formulario
     * a /logout para salir de una vista de acceso restringido.
     */
    public function salir()
    {
        session()->destroy();

        return redirect()->to('/login')->with('success', 'Sesión cerrada correctamente.');
    }

    /**
     * Devuelve la URL que el usuario intentaba abrir (la guardó AuthFilter) o
     * el panel general.
     *
     * Solo se aceptan rutas internas: si el valor no es relativo al sitio se
     * descarta, para no convertir el login en un redirector abierto.
     */
    private function destinoPendiente(): string
    {
        $base     = base_url();
        $guardado = (string) (session('redirect_to') ?? '');
        session()->remove('redirect_to');

        // current_url() guarda una URL absoluta ("http://host/ruta"); se le
        // quita el prefijo del sitio y se normaliza a ruta con "/" inicial.
        if (str_starts_with($guardado, $base)) {
            $guardado = '/' . ltrim(substr($guardado, strlen($base)), '/');
        }

        // Debe quedar una ruta interna: ni vacía, ni protocolo-relativo (//evil.com),
        // ni con barras invertidas, ni apuntando a /login o /logout (bucle).
        if (! str_starts_with($guardado, '/')
            || str_starts_with($guardado, '//')
            || str_starts_with($guardado, '/\\')
            || in_array(rtrim($guardado, '/'), ['/login', '/logout'], true)
        ) {
            return $base;
        }

        return $guardado;
    }
}
