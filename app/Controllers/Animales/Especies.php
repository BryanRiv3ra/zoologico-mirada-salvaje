<?php

namespace App\Controllers\Animales;

use App\Controllers\BaseController;
use App\Models\Core\EspecieModel;

/**
 * Gestión de especies (CU: Gestionar especies).
 * Permiso: solo administrador (definido en app/Config/Routes.php).
 *
 * No hay columna de baja en core.especies, así que una especie no se desactiva:
 * o se conserva o no se usa. Por eso no existe "desactivar"; lo que hay es
 * impedir el borrado de una especie que ya tenga animales asociados, para no
 * dejar huérfano a ningún animal.
 */
class Especies extends BaseController
{
    protected EspecieModel $especies;

    public function __construct()
    {
        $this->especies = model(EspecieModel::class);
    }

    public function index(): string
    {
        // El contador de animales se pide por especie para poder avisar en el
        // listado de cuáles no se pueden borrar.
        $especies = $this->especies->listaCompleta();

        foreach ($especies as &$especie) {
            $especie['animales'] = $this->especies->contarAnimales((int) $especie['id']);
        }
        unset($especie);

        return view('animales/especies/index', [
            'titulo'      => 'Especies',
            'cssExtra'    => 'animales.css',
            'especies'    => $especies,
            'puedeEditar' => $this->esAdministrador(),
        ]);
    }

    /**
     * El CRUD de especies entero es de escritura, así que la ruta ya es de
     * solo administrador. Esto solo evita mostrar controles inútiles.
     */
    protected function esAdministrador(): bool
    {
        return in_array('administrador', (array) session('roles'), true);
    }

    public function nueva(): string
    {
        return view('animales/especies/form', [
            'titulo'   => 'Nueva Especie',
            'cssExtra' => 'animales.css',
            'modo'     => 'crear',
            'especie'  => ['id' => null, 'nombre_comun' => '', 'nombre_cientifico' => '', 'descripcion' => ''],
        ]);
    }

    public function editar(int $id)
    {
        $especie = $this->especies->find($id);

        if ($especie === null) {
            return $this->noExiste('La especie no existe.');
        }

        return view('animales/especies/form', [
            'titulo'   => 'Editar Especie',
            'cssExtra' => 'animales.css',
            'modo'     => 'editar',
            'especie'  => $especie,
        ]);
    }

    public function guardar()
    {
        $datos = $this->validarDatos();

        if ($datos === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($this->especies->existeNombreComun($datos['nombre_comun'])) {
            return redirect()->back()->withInput()->with('error', 'Ya existe una especie con ese nombre común.');
        }

        if ($datos['nombre_cientifico'] !== null && $this->especies->existeNombreCientifico($datos['nombre_cientifico'])) {
            return redirect()->back()->withInput()->with('error', 'Ya existe una especie con ese nombre científico.');
        }

        $this->especies->insert($datos);

        return redirect()->to('animales/especies')->with('success', 'Especie creada correctamente.');
    }

    public function actualizar(int $id)
    {
        if ($this->especies->find($id) === null) {
            return $this->noExiste('La especie no existe.');
        }

        $datos = $this->validarDatos();

        if ($datos === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($this->especies->existeNombreComun($datos['nombre_comun'], $id)) {
            return redirect()->back()->withInput()->with('error', 'Ya existe otra especie con ese nombre común.');
        }

        if ($datos['nombre_cientifico'] !== null && $this->especies->existeNombreCientifico($datos['nombre_cientifico'], $id)) {
            return redirect()->back()->withInput()->with('error', 'Ya existe otra especie con ese nombre científico.');
        }

        $this->especies->update($id, $datos);

        return redirect()->to('animales/especies')->with('success', 'Especie actualizada correctamente.');
    }

    /**
     * Borrado de una especie SOLO si no tiene animales asociados.
     *
     * core.animales.especie_id es NOT NULL con FK a core.especies, así que
     * borrar una especie en uso dejaría animales sin especie (y la FK lo
     * impediría con un error 500). Se comprueba antes y se explica.
     */
    public function eliminar(int $id)
    {
        if ($this->especies->find($id) === null) {
            return $this->noExiste('La especie no existe.');
        }

        $animales = $this->especies->contarAnimales($id);

        if ($animales > 0) {
            return redirect()
                ->to('animales/especies')
                ->with('error', 'No se puede eliminar: hay ' . $animales . ' animal(es) con esta especie. Reasígnalos primero o dales de baja.');
        }

        $this->especies->delete($id);

        return redirect()->to('animales/especies')->with('success', 'Especie eliminada correctamente.');
    }

    /**
     * @return array<string, string|null>|null Null si la validación falla.
     */
    protected function validarDatos(): ?array
    {
        $reglas = [
            'nombre_comun'      => 'required|max_length[100]',
            'nombre_cientifico' => 'permit_empty|max_length[150]',
            'descripcion'       => 'permit_empty',
        ];

        if (! $this->validate($reglas)) {
            return null;
        }

        $cientifico = trim((string) $this->request->getPost('nombre_cientifico'));

        return [
            'nombre_comun'      => trim((string) $this->request->getPost('nombre_comun')),
            'nombre_cientifico' => $cientifico === '' ? null : $cientifico,
            'descripcion'       => $this->request->getPost('descripcion') === '' ? null : (string) $this->request->getPost('descripcion'),
        ];
    }

    protected function noExiste(string $mensaje)
    {
        return redirect()->to('animales/especies')->with('error', $mensaje);
    }
}
