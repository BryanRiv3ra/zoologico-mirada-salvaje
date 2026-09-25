<?php

namespace App\Controllers\Entradas;

use App\Controllers\BaseController;
use App\Models\Entradas\TarifaModel;

/**
 * Gestión de tarifas de ingreso. Permiso: admin_mercadeo, administrador.
 */
class Tarifas extends BaseController
{
    protected $helpers = ['url', 'form'];

    private TarifaModel $tarifas;

    public function __construct()
    {
        $this->tarifas = model(TarifaModel::class);
    }

    public function index(): string
    {
        return view('entradas/tarifas/index', [
            'titulo'   => 'Tarifas',
            'cssExtra' => 'entradas.css',
            'tarifas'  => $this->tarifas->todas(),
        ]);
    }

    public function nueva(): string
    {
        return view('entradas/tarifas/form', [
            'titulo'   => 'Nueva tarifa',
            'cssExtra' => 'entradas.css',
            'tarifa'   => null,
            'tipos'    => $this->tiposVisitante(),
        ]);
    }

    public function guardar()
    {
        $datos = [
            'nombre'         => $this->request->getPost('nombre'),
            'tipo_visitante' => $this->request->getPost('tipo_visitante'),
            'precio'         => $this->request->getPost('precio'),
            'activo'         => true,
        ];

        if (! $this->tarifas->validate($datos)) {
            return redirect()->back()->withInput()->with('errors', $this->tarifas->errors());
        }

        $this->tarifas->insert($datos);

        return redirect()->to('/entradas/tarifas')->with('success', 'Tarifa creada correctamente.');
    }

    public function editar(int $id): string
    {
        $tarifa = $this->tarifas->find($id);
        if ($tarifa === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('entradas/tarifas/form', [
            'titulo'   => 'Editar tarifa',
            'cssExtra' => 'entradas.css',
            'tarifa'   => $tarifa,
            'tipos'    => $this->tiposVisitante(),
        ]);
    }

    public function actualizar(int $id)
    {
        $tarifa = $this->tarifas->find($id);
        if ($tarifa === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $datos = [
            'nombre'         => $this->request->getPost('nombre'),
            'tipo_visitante' => $this->request->getPost('tipo_visitante'),
            'precio'         => $this->request->getPost('precio'),
            'activo'         => $this->request->getPost('activo') ? true : false,
        ];

        if (! $this->tarifas->validate($datos)) {
            return redirect()->back()->withInput()->with('errors', $this->tarifas->errors());
        }

        $this->tarifas->update($id, $datos);

        return redirect()->to('/entradas/tarifas')->with('success', 'Tarifa actualizada correctamente.');
    }

    public function desactivar(int $id)
    {
        $this->tarifas->update($id, ['activo' => false]);

        return redirect()->back()->with('success', 'Tarifa desactivada.');
    }

    private function tiposVisitante(): array
    {
        return [
            'adulto'         => 'Adulto (12 - 59 años)',
            'nino'           => 'Niño (3 - 11 años)',
            'tercera_edad'   => 'Adulto mayor (60+ años)',
            'estudiante'     => 'Estudiante',
            'grupo_escolar'  => 'Grupo escolar',
            'docente'        => 'Docente',
            'otro'           => 'Otro',
        ];
    }
}