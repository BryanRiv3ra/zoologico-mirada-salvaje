<?php

namespace App\Controllers\Limpieza;

use App\Controllers\BaseController;
use App\Models\Core\InventarioModel;
use App\Models\Core\ZonaModel;
use App\Models\Limpieza\TareaInsumoModel;
use App\Models\Limpieza\TareaLimpiezaModel;

/**
 * Gestión de tareas de limpieza (CU: Gestionar tareas de limpieza, Asociar insumos).
 * Permiso: administrador, supervisor.
 */
class Tareas extends BaseController
{
    protected TareaLimpiezaModel $tareas;
    protected TareaInsumoModel $insumos;
    protected ZonaModel $zonas;
    protected InventarioModel $inventario;

    public function __construct()
    {
        $this->tareas    = model(TareaLimpiezaModel::class);
        $this->insumos   = model(TareaInsumoModel::class);
        $this->zonas     = model(ZonaModel::class);
        $this->inventario = model(InventarioModel::class);
    }

    public function index(): string
    {
        $db = db_connect();

        $tareas = $db->table('limpieza.tareas_limpieza as t')
            ->select('t.*, z.nombre as zona_nombre')
            ->join('core.zonas as z', 'z.id = t.zona_id')
            ->orderBy('t.activo', 'desc')
            ->orderBy('z.nombre', 'asc')
            ->orderBy('t.descripcion', 'asc')
            ->get()
            ->getResultArray();

        return view('limpieza/tareas/index', [
            'titulo' => 'Tareas de Limpieza',
            'tareas' => $tareas,
        ]);
    }

    public function nueva(): string
    {
        return view('limpieza/tareas/form', [
            'titulo' => 'Nueva Tarea',
            'modo'   => 'crear',
            'tarea'  => ['id' => null, 'zona_id' => '', 'descripcion' => '', 'frecuencia' => '', 'activo' => true],
            'zonas'  => $this->zonas->listaActivas(),
        ]);
    }

    public function editar(int $id)
    {
        $tarea = $this->tareas->find($id);
        if ($tarea === null) {
            return redirect()->to('limpieza/tareas')->with('error', 'La tarea no existe.');
        }

        return view('limpieza/tareas/form', [
            'titulo' => 'Editar Tarea',
            'modo'   => 'editar',
            'tarea'  => $tarea,
            'zonas'  => $this->zonas->listaActivas(),
        ]);
    }

    public function guardar()
    {
        $datos = $this->validarDatos();
        if ($datos === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Solo se crean tareas en zonas activas.
        $zona = $this->zonas->find($datos['zona_id']);
        if ($zona === null || (int) $zona['activo'] !== 1) {
            return redirect()->back()->withInput()->with('error', 'Solo se pueden crear tareas en zonas activas.');
        }

        if ($this->tareas->existeDescripcion($datos['zona_id'], $datos['descripcion'])) {
            return redirect()->back()->withInput()->with('error', 'Ya existe una tarea con esa descripción en la zona seleccionada.');
        }

        $this->tareas->insert($datos);

        return redirect()->to('limpieza/tareas')->with('success', 'Tarea creada correctamente.');
    }

    public function actualizar(int $id)
    {
        if ($this->tareas->find($id) === null) {
            return redirect()->to('limpieza/tareas')->with('error', 'La tarea no existe.');
        }

        $datos = $this->validarDatos();
        if ($datos === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($this->tareas->existeDescripcion($datos['zona_id'], $datos['descripcion'], $id)) {
            return redirect()->back()->withInput()->with('error', 'Ya existe otra tarea con esa descripción en la zona seleccionada.');
        }

        $this->tareas->update($id, $datos);

        return redirect()->to('limpieza/tareas')->with('success', 'Tarea actualizada correctamente.');
    }

    public function desactivar(int $id)
    {
        if ($this->tareas->find($id) === null) {
            return redirect()->to('limpieza/tareas')->with('error', 'La tarea no existe.');
        }

        $this->tareas->update($id, ['activo' => false]);

        return redirect()->to('limpieza/tareas')->with('success', 'Tarea desactivada.');
    }

    /**
     * Asociación de insumos a una tarea.
     */
    public function insumos(int $tareaId)
    {
        $tarea = $this->tareas->find($tareaId);
        if ($tarea === null) {
            return redirect()->to('limpieza/tareas')->with('error', 'La tarea no existe.');
        }

        $asignados = $this->insumos->deTarea($tareaId);
        $idsAsignados = array_column($asignados, 'inventario_id');

        $disponibles = array_filter(
            $this->inventario->insumosLimpieza(),
            static fn (array $insumo): bool => ! in_array((int) $insumo['id'], $idsAsignados, true)
        );

        return view('limpieza/tareas/insumos', [
            'titulo'       => 'Insumos de la Tarea',
            'tarea'        => $tarea,
            'asignados'    => $asignados,
            'disponibles'  => $disponibles,
        ]);
    }

    public function agregarInsumo(int $tareaId)
    {
        if ($this->tareas->find($tareaId) === null) {
            return redirect()->to('limpieza/tareas')->with('error', 'La tarea no existe.');
        }

        $reglas = [
            'inventario_id' => 'required|is_natural_no_zero',
            'cantidad'      => 'required|greater_than[0]',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $inventarioId = (int) $this->request->getPost('inventario_id');
        $cantidad     = (float) $this->request->getPost('cantidad');

        // Validación backend: solo insumos de tipo 'limpieza'.
        $insumo = $this->inventario->find($inventarioId);
        if ($insumo === null || $insumo['tipo'] !== 'limpieza') {
            return redirect()->back()->with('error', 'El insumo seleccionado no es válido para limpieza.');
        }

        if (! $this->insumos->agregar($tareaId, $inventarioId, $cantidad)) {
            return redirect()->back()->with('error', 'El insumo ya está asociado a esta tarea o la cantidad no es válida.');
        }

        return redirect()->to('limpieza/tareas/insumos/' . $tareaId)->with('success', 'Insumo asociado a la tarea.');
    }

    public function quitarInsumo(int $insumoId)
    {
        $relacion = $this->insumos->find($insumoId);
        if ($relacion === null) {
            return redirect()->to('limpieza/tareas')->with('error', 'La relación de insumo no existe.');
        }

        $tareaId = (int) $relacion['tarea_id'];
        $this->insumos->delete($insumoId);

        return redirect()->to('limpieza/tareas/insumos/' . $tareaId)->with('success', 'Insumo retirado de la tarea.');
    }

    protected function validarDatos(): ?array
    {
        $reglas = [
            'zona_id'     => 'required|is_natural_no_zero',
            'descripcion' => 'required|max_length[255]',
            'frecuencia'  => 'permit_empty|max_length[100]',
        ];

        if (! $this->validate($reglas)) {
            return null;
        }

        return [
            'zona_id'     => (int) $this->request->getPost('zona_id'),
            'descripcion' => trim($this->request->getPost('descripcion')),
            'frecuencia'  => trim((string) $this->request->getPost('frecuencia')),
        ];
    }
}