<?php

namespace App\Controllers\Limpieza;

use App\Controllers\BaseController;
use App\Libraries\Limpieza\ServicioAsignaciones;
use App\Models\Core\EmpleadoModel;
use App\Models\Limpieza\TareaLimpiezaModel;

/**
 * Asignación de tareas a empleados (CU: Asignar tarea a empleado, Reasignar tarea).
 * Permiso: supervisor.
 */
class Asignaciones extends BaseController
{
    protected ServicioAsignaciones $servicio;
    protected TareaLimpiezaModel $tareas;
    protected EmpleadoModel $empleados;

    public function __construct()
    {
        $this->servicio  = new ServicioAsignaciones();
        $this->tareas    = model(TareaLimpiezaModel::class);
        $this->empleados = model(EmpleadoModel::class);
    }

    public function nueva(): string
    {
        return view('limpieza/asignaciones/form', [
            'titulo'    => 'Asignar Tarea',
            'tareas'    => $this->tareas->listaActivas(),
            'empleados' => $this->empleados->activosConRol('empleado_limpieza'),
        ]);
    }

    public function guardar()
    {
        $reglas = [
            'tarea_id'         => 'required|is_natural_no_zero',
            'empleado_id'      => 'required|is_natural_no_zero',
            'fecha_programada' => 'required|valid_date[Y-m-d]',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fecha = $this->request->getPost('fecha_programada');
        if ($fecha < date('Y-m-d')) {
            return redirect()->back()->withInput()->with('error', 'La fecha programada no puede ser anterior a hoy.');
        }

        // asignado_por siempre es el usuario de la sesión, nunca el formulario.
        $resultado = $this->servicio->asignar(
            (int) $this->request->getPost('tarea_id'),
            (int) $this->request->getPost('empleado_id'),
            (int) session('usuario_id'),
            $fecha
        );

        if (isset($resultado['error'])) {
            return redirect()->back()->withInput()->with('error', $resultado['error']);
        }

        return redirect()->to('limpieza/seguimiento')->with('success', 'Tarea asignada correctamente.');
    }

    /**
     * Reasignación: cambia el empleado solo si la asignación está pendiente.
     */
    public function reasignar(int $id)
    {
        $asignacion = model(\App\Models\Limpieza\AsignacionLimpiezaModel::class)->conDetalles($id);

        if ($asignacion === null) {
            return redirect()->to('limpieza/seguimiento')->with('error', 'La asignación no existe.');
        }

        if ($asignacion['estado'] !== 'pendiente') {
            return redirect()->to('limpieza/seguimiento')->with('error', 'Solo se reasignan asignaciones pendientes.');
        }

        return view('limpieza/asignaciones/reasignar', [
            'titulo'      => 'Reasignar Tarea',
            'asignacion'  => $asignacion,
            'empleados'   => $this->empleados->activosConRol('empleado_limpieza'),
        ]);
    }

    public function guardarReasignacion(int $id)
    {
        $reglas = ['empleado_id' => 'required|is_natural_no_zero'];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $resultado = $this->servicio->reasignar($id, (int) $this->request->getPost('empleado_id'));

        if (isset($resultado['error'])) {
            return redirect()->back()->with('error', $resultado['error']);
        }

        return redirect()->to('limpieza/seguimiento')->with('success', 'Tarea reasignada correctamente.');
    }
}