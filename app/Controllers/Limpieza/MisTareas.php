<?php

namespace App\Controllers\Limpieza;

use App\Controllers\BaseController;
use App\Libraries\Limpieza\AsignacionException;
use App\Libraries\Limpieza\ServicioAsignaciones;
use App\Models\Limpieza\AsignacionLimpiezaModel;
use App\Models\Limpieza\RegistroLimpiezaModel;
use App\Models\Limpieza\TareaInsumoModel;

/**
 * Mis asignaciones (empleado de limpieza).
 * El empleado SOLO ve y modifica sus propias asignaciones (session('empleado_id')).
 * Permiso: empleado_limpieza.
 */
class MisTareas extends BaseController
{
    protected AsignacionLimpiezaModel $asignaciones;
    protected TareaInsumoModel $insumos;
    protected RegistroLimpiezaModel $registros;
    protected ServicioAsignaciones $servicio;

    public function __construct()
    {
        $this->asignaciones = model(AsignacionLimpiezaModel::class);
        $this->insumos      = model(TareaInsumoModel::class);
        $this->registros    = model(RegistroLimpiezaModel::class);
        $this->servicio     = new ServicioAsignaciones();
    }

    public function index(): string
    {
        $empleadoId = (int) session('empleado_id');
        $fecha      = $this->request->getGet('fecha') ?? '';

        $db = db_connect();

        $query = $db->table('limpieza.asignaciones_limpieza as a')
            ->select('a.*, t.descripcion, t.frecuencia, z.nombre as zona_nombre, z.tipo as zona_tipo')
            ->join('limpieza.tareas_limpieza as t', 't.id = a.tarea_id')
            ->join('core.zonas as z', 'z.id = t.zona_id')
            ->where('a.empleado_id', $empleadoId)
            ->orderBy('a.fecha_programada', 'asc');

        if ($fecha !== '') {
            $query->where('a.fecha_programada', $fecha);
        }

        return view('limpieza/mis_tareas/index', [
            'titulo'    => 'Mis Asignaciones',
            'asignaciones' => $query->get()->getResultArray(),
            'fecha'     => $fecha,
        ]);
    }

    public function detalle(int $id): string
    {
        $asignacion = $this->asignaciones->conDetalles($id);
        if ($asignacion === null) {
            return redirect()->to('limpieza/mis-tareas')->with('error', 'La asignación no existe.');
        }

        // Verificación de propietario.
        if ((int) $asignacion['empleado_id'] !== (int) session('empleado_id')) {
            return $this->sinPermiso();
        }

        return view('limpieza/mis_tareas/detalle', [
            'titulo'     => 'Detalle de Asignación',
            'asignacion' => $asignacion,
            'insumos'    => $this->insumos->deTarea((int) $asignacion['tarea_id']),
            'registro'   => $this->registros->where('asignacion_id', $id)->first(),
        ]);
    }

    /**
     * Inicia la tarea (pendiente → en_curso).
     */
    public function iniciar(int $id)
    {
        try {
            $this->servicio->iniciar($id, (int) session('empleado_id'));
        } catch (AsignacionException $e) {
            return $this->manejarError($e);
        }

        return redirect()->to('limpieza/mis-tareas/detalle/' . $id)->with('success', 'Tarea iniciada. ¡A trabajar!');
    }

    /**
     * Finaliza la tarea (en_curso → listo) y crea el registro de limpieza.
     */
    public function finalizar(int $id)
    {
        $observaciones = (string) $this->request->getPost('observaciones');

        try {
            $this->servicio->finalizar($id, (int) session('empleado_id'), $observaciones);
        } catch (AsignacionException $e) {
            return $this->manejarError($e);
        }

        return redirect()->to('limpieza/mis-tareas/detalle/' . $id)->with('success', 'Tarea finalizada y registrada.');
    }

    /**
     * Registra o actualiza observaciones sobre una asignación propia.
     */
    public function observaciones(int $id)
    {
        $observaciones = (string) $this->request->getPost('observaciones');
        if (trim($observaciones) === '') {
            return redirect()->back()->with('error', 'La observación es obligatoria.');
        }

        try {
            $this->servicio->registrarObservaciones($id, (int) session('empleado_id'), $observaciones);
        } catch (AsignacionException $e) {
            return $this->manejarError($e);
        }

        return redirect()->back()->with('success', 'Observaciones guardadas.');
    }

    protected function manejarError(AsignacionException $e)
    {
        if ($e->getCode() === 403) {
            return $this->sinPermiso();
        }

        return redirect()->back()->with('error', $e->getMessage());
    }

    protected function sinPermiso()
    {
        return service('response')
            ->setStatusCode(403)
            ->setBody(view('errors/prohibido', [
                'nombre'  => session('nombre') ?? 'Usuario',
                'roles'   => (array) session('roles'),
                'rolesOk' => ['la asignación te pertenece'],
            ]));
    }
}