<?php

namespace App\Controllers\Animales;

use App\Controllers\BaseController;
use App\Models\AnimalModel;
use App\Models\Core\EspecieModel;
use App\Models\Core\ZonaModel;

/**
 * Gestión de animales (CU: Gestionar animales).
 *
 * Permisos (definidos en app/Config/Routes.php):
 *   listado            → administrador, supervisor
 *   crear/editar/baja  → administrador
 *
 * Baja LÓGICA, nunca borrado: clinico.historial_clinico,
 * clinico.aplicaciones_vacunas y alimentacion.dietas tienen FK hacia
 * core.animales con ON DELETE CASCADE, así que un DELETE destruiría el historial
 * clínico, las vacunas y las dietas del animal. La baja cambia `estado` a
 * 'fallecido' o 'trasladado' (los dos estados de baja del CHECK real).
 *
 * core.animales NO tiene columna para el motivo, así que la baja no lo pide.
 */
class Animales extends BaseController
{
    protected AnimalModel $animales;
    protected EspecieModel $especies;
    protected ZonaModel $zonas;

    public function __construct()
    {
        $this->animales = model(AnimalModel::class);
        $this->especies = model(EspecieModel::class);
        $this->zonas    = model(ZonaModel::class);
    }

    public function index(): string
    {
        $filtros = [
            'especie_id' => $this->request->getGet('especie_id'),
            'zona_id'    => $this->request->getGet('zona_id'),
            'estado'     => $this->request->getGet('estado'),
            'busqueda'   => $this->request->getGet('busqueda'),
        ];

        return view('animales/index', [
            'titulo'      => 'Animales',
            'cssExtra'    => 'animales.css',
            'animales'    => $this->animales->listar($filtros),
            'especies'    => $this->especies->listaCompleta(),
            'zonas'       => $this->zonas->listaActivas(),
            'estados'     => AnimalModel::ESTADOS,
            'filtros'     => $filtros,
            'puedeEditar' => $this->esAdministrador(),
        ]);
    }

    /**
     * Solo el administrador puede escribir. El supervisor puede ver el listado
     * (regla de la ruta), pero no tiene sentido mostrarle botones que lo
     * llevarían a un 403.
     */
    protected function esAdministrador(): bool
    {
        return in_array('administrador', (array) session('roles'), true);
    }

    public function nuevo(): string
    {
        return view('animales/form', [
            'titulo'   => 'Nuevo Animal',
            'cssExtra' => 'animales.css',
            'modo'     => 'crear',
            'animal'   => $this->animalVacio(),
            'especies' => $this->especies->listaCompleta(),
            'zonas'    => $this->zonas->listaActivas(),
            'sexos'    => AnimalModel::SEXOS,
        ]);
    }

    public function editar(int $id)
    {
        $animal = $this->animales->findConRelaciones($id);

        if ($animal === null) {
            return $this->noExiste('El animal no existe.');
        }

        return view('animales/form', [
            'titulo'   => 'Editar Animal',
            'cssExtra' => 'animales.css',
            'modo'     => 'editar',
            'animal'   => $animal,
            'especies' => $this->especies->listaCompleta(),
            'zonas'    => $this->zonas->listaActivas(),
            'sexos'    => AnimalModel::SEXOS,
        ]);
    }

    public function guardar()
    {
        $datos = $this->validarDatos();

        if ($datos === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->animales->insert($datos);

        return redirect()->to('animales')->with('success', 'Animal registrado correctamente.');
    }

    public function actualizar(int $id)
    {
        if ($this->animales->find($id) === null) {
            return $this->noExiste('El animal no existe.');
        }

        $datos = $this->validarDatos();

        if ($datos === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->animales->update($id, $datos);

        return redirect()->to('animales')->with('success', 'Animal actualizado correctamente.');
    }

    /**
     * Baja lógica: cambia `estado` a 'fallecido' o 'trasladado'.
     *
     * NUNCA borra la fila. Además, si el animal ya está dado de baja no se
     * repite la operación (evita el segundo clic silencioso).
     */
    public function darDeBaja(int $id)
    {
        $animal = $this->animales->find($id);

        if ($animal === null) {
            return $this->noExiste('El animal no existe.');
        }

        $estadoActual = (string) $animal['estado'];

        if (in_array($estadoActual, AnimalModel::ESTADOS_BAJA, true)) {
            return redirect()
                ->to('animales')
                ->with('error', 'Este animal ya estaba dado de baja (' . AnimalModel::ESTADOS[$estadoActual] . ').');
        }

        $nuevoEstado = $this->request->getPost('estado') ?? 'fallecido';

        // Solo se admiten los estados de baja que acepta el CHECK real.
        if (! in_array($nuevoEstado, AnimalModel::ESTADOS_BAJA, true)) {
            return redirect()
                ->to('animales')
                ->with('error', 'Estado de baja no válido. Usa "fallecido" o "trasladado".');
        }

        $this->animales->update($id, ['estado' => $nuevoEstado]);

        return redirect()
            ->to('animales')
            ->with('success', 'Animal dado de baja (' . AnimalModel::ESTADOS[$nuevoEstado] . '). Se conserva su historial.');
    }

    /**
     * Devuelve un animal de alta sin datos, con el estado por defecto del
     * DEFAULT de la BD ('activo').
     *
     * @return array<string, mixed>
     */
    protected function animalVacio(): array
    {
        return [
            'id'                => null,
            'nombre'            => '',
            'especie_id'        => null,
            'zona_id'           => null,
            'fecha_nacimiento'  => null,
            'sexo'              => null,
            'estado'            => 'activo',
        ];
    }

    /**
     * Valida y normaliza el formulario.
     *
     * Hay dos clases de error, y las dos terminan en `null`:
     *   - Las reglas de CI4: el controlador adjunta `with('errors', ...)`.
     *   - Las reglas de negocio (especie o zona que no existe, fecha futura):
     *     aquí se deja el flash de error y el controlador solo hace withInput().
     * Un `errors` vacío no se pinta porque templates/alertas.php comprueba
     * `$erroresValidacion !== []`.
     *
     * @return array<string, mixed>|null Null si no se debe guardar nada.
     */
    protected function validarDatos(): ?array
    {
        $reglas = [
            'nombre'            => 'required|max_length[100]',
            'especie_id'        => 'required|is_natural_no_zero',
            'zona_id'           => 'permit_empty|is_natural_no_zero',
            // Con formato explícito: sin él, valid_date usa strtotime(), que
            // acepta "2026-13-45" y otras basura. Y valida que el día exista.
            'fecha_nacimiento'  => 'permit_empty|valid_date[Y-m-d]',
            'sexo'              => 'permit_empty|in_list[M,F,N]',
            'estado'            => 'required|in_list[activo,en_tratamiento,fallecido,trasladado]',
        ];

        if (! $this->validate($reglas)) {
            return null;
        }

        // zona_id puede llegar de tres formas: el valor "Sin asignar"
        // (cadena vacía), un id numérico, o ausente por completo (null) cuando
        // el campo no se envía. Los dos primeros se tratan como "sin zona";
        // sin la comprobación de null, (int) null daría 0 y dispararía el
        // chequeo de zona con un id inexistente.
        $zonaRaw = $this->request->getPost('zona_id');

        $datos = [
            'nombre'     => trim((string) $this->request->getPost('nombre')),
            'especie_id' => (int) $this->request->getPost('especie_id'),
            'zona_id'    => ($zonaRaw === '' || $zonaRaw === null) ? null : (int) $zonaRaw,
            'sexo'       => $this->request->getPost('sexo') ?: null,
            'estado'     => (string) $this->request->getPost('estado'),
        ];

        // La especie debe existir: el formulario lista core.especies, pero el
        // POST se puede manipular a mano.
        if ($this->especies->find($datos['especie_id']) === null) {
            $this->setFlashError('La especie seleccionada no existe.');

            return null;
        }

        // La zona, si se envió, debe existir y estar activa.
        if ($datos['zona_id'] !== null) {
            if (! $this->animales->zonaActivaExiste($datos['zona_id'])) {
                $this->setFlashError('La zona seleccionada no existe o está inactiva.');

                return null;
            }
        }

        // Fecha de nacimiento: valid_date[Y-m-d] ya garantiza que el día exista
        // ("2026-02-30" se rechaza). Aquí solo queda descartar el futuro.
        $fecha = trim((string) $this->request->getPost('fecha_nacimiento'));

        if ($fecha === '') {
            $datos['fecha_nacimiento'] = null;
        } else {
            if (new \DateTimeImmutable($fecha) > new \DateTimeImmutable('today')) {
                $this->setFlashError('La fecha de nacimiento no puede ser futura.');

                return null;
            }

            $datos['fecha_nacimiento'] = $fecha;
        }

        return $datos;
    }

    /**
     * Flash de error para las reglas de negocio (las que no cubre CI4).
     *
     * Se usa session() y no $this->session porque BaseController no expone esa
     * propiedad: viene comentada en app/Controllers/BaseController.php.
     */
    protected function setFlashError(string $mensaje): void
    {
        session()->setFlashdata('error', $mensaje);
    }

    protected function noExiste(string $mensaje)
    {
        return redirect()->to('animales')->with('error', $mensaje);
    }
}
