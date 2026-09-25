<?php

namespace App\Controllers\Limpieza;

use App\Controllers\BaseController;
use App\Models\Core\EmpleadoModel;
use App\Models\Core\ZonaModel;
use App\Models\Limpieza\AsignacionLimpiezaModel;

/**
 * Seguimiento general de asignaciones (CU: Ver seguimiento general).
 * Campo de texto con 3 grupos: Pendiente / En curso / Listo.
 * Permiso: supervisor (y administrador en modo lectura).
 */
class Seguimiento extends BaseController
{
    protected AsignacionLimpiezaModel $asignaciones;
    protected ZonaModel $zonas;
    protected EmpleadoModel $empleados;

    public function __construct()
    {
        $this->asignaciones = model(AsignacionLimpiezaModel::class);
        $this->zonas        = model(ZonaModel::class);
        $this->empleados    = model(EmpleadoModel::class);
    }

    public function index(): string
    {
        $fecha   = $this->request->getGet('fecha') ?? date('Y-m-d');
        $zonaId  = (int) ($this->request->getGet('zona') ?? 0);
        $empId   = (int) ($this->request->getGet('empleado') ?? 0);

        $db = db_connect();

        $query = $db->table('limpieza.asignaciones_limpieza as a')
            ->select("a.*, t.descripcion, t.frecuencia, z.nombre as zona_nombre, z.tipo as zona_tipo,
                      e.nombre as empleado_nombre, e.apellido as empleado_apellido, r.observaciones")
            ->join('limpieza.tareas_limpieza as t', 't.id = a.tarea_id')
            ->join('core.zonas as z', 'z.id = t.zona_id')
            ->join('core.empleados as e', 'e.id = a.empleado_id')
            ->join('limpieza.registros_limpieza as r', 'r.asignacion_id = a.id', 'left')
            ->where('a.fecha_programada', $fecha)
            ->orderBy('z.nombre', 'asc')
            ->orderBy('t.descripcion', 'asc');

        if ($zonaId > 0) {
            $query->where('t.zona_id', $zonaId);
        }
        if ($empId > 0) {
            $query->where('a.empleado_id', $empId);
        }

        $asignaciones = $query->get()->getResultArray();

        $porEstado = [
            'pendiente' => array_values(array_filter($asignaciones, fn ($a) => $a['estado'] === 'pendiente')),
            'en_curso'  => array_values(array_filter($asignaciones, fn ($a) => $a['estado'] === 'en_curso')),
            'listo'     => array_values(array_filter($asignaciones, fn ($a) => $a['estado'] === 'listo')),
        ];

        return view('limpieza/seguimiento/tablero', [
            'titulo'       => 'Seguimiento de Limpieza',
            'fecha'        => $fecha,
            'porEstado'    => $porEstado,
            'conteos'      => [
                'pendiente' => count($porEstado['pendiente']),
                'en_curso'  => count($porEstado['en_curso']),
                'listo'     => count($porEstado['listo']),
            ],
            'zonas'        => $this->zonas->listaActivas(),
            'empleados'    => $this->empleados->activosConRol('empleado_limpieza'),
            'zonaFiltro'   => $zonaId,
            'empleadoFiltro' => $empId,
        ]);
    }

    /**
     * Detalle de una asignación (incluye observaciones e insumos).
     * Permiso: lectura para supervisores/administradores.
     */
    public function detalle(int $id): string
    {
        $asignacion = $this->asignaciones->conDetalles($id);
        if ($asignacion === null) {
            return redirect()->to('limpieza/seguimiento')->with('error', 'La asignación no existe.');
        }

        $insumos = model(\App\Models\Limpieza\TareaInsumoModel::class)->deTarea((int) $asignacion['tarea_id']);

        $registro = model(\App\Models\Limpieza\RegistroLimpiezaModel::class)
            ->where('asignacion_id', $id)
            ->first();

        return view('limpieza/seguimiento/detalle', [
            'titulo'     => 'Detalle de Asignación',
            'asignacion' => $asignacion,
            'insumos'    => $insumos,
            'registro'   => $registro,
        ]);
    }
}