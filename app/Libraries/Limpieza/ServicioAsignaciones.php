<?php

namespace App\Libraries\Limpieza;

use App\Models\Core\EmpleadoModel;
use App\Models\Limpieza\AsignacionLimpiezaModel;
use App\Models\Limpieza\RegistroLimpiezaModel;
use App\Models\Limpieza\TareaLimpiezaModel;

/**
 * Lógica de asignección, transiciones y registros del módulo de Limpieza.
 * Refleja la clase «Asignacion» del diagrama de clases (asignar, iniciar, finalizar).
 */
class ServicioAsignaciones
{
    protected AsignacionLimpiezaModel $asignaciones;
    protected RegistroLimpiezaModel $registros;
    protected TareaLimpiezaModel $tareas;
    protected EmpleadoModel $empleados;

    public function __construct()
    {
        $this->asignaciones = model(AsignacionLimpiezaModel::class);
        $this->registros    = model(RegistroLimpiezaModel::class);
        $this->tareas       = model(TareaLimpiezaModel::class);
        $this->empleados    = model(EmpleadoModel::class);
    }

    /**
     * Asigna una tarea activa a un empleado activo con rol 'empleado_limpieza'.
     * `asignadoPor` es el usuario_id de la sesión (nunca viene del formulario).
     *
     * @return array ['ok' => true, 'id' => int] o ['error' => string]
     */
    public function asignar(int $tareaId, int $empleadoId, int $asignadoPor, string $fechaProgramada): array
    {
        $tarea = $this->tareas->find($tareaId);
        if ($tarea === null || (int) $tarea['activo'] !== 1) {
            return ['error' => 'La tarea seleccionada no existe o está desactivada.'];
        }

        $empleado = $this->empleados->find($empleadoId);
        if ($empleado === null || (int) $empleado['activo'] !== 1) {
            return ['error' => 'El empleado seleccionado no existe o no está activo.'];
        }

        // Solo empleados activos con rol 'empleado_limpieza' reciben asignaciones.
        $esEmpleadoLimpieza = false;
        foreach ($this->empleados->activosConRol('empleado_limpieza') as $candidato) {
            if ((int) $candidato['id'] === $empleadoId) {
                $esEmpleadoLimpieza = true;
                break;
            }
        }
        if (! $esEmpleadoLimpieza) {
            return ['error' => 'El empleado seleccionado no tiene el rol de empleado de limpieza.'];
        }

        if ($this->asignaciones->tareaExisteEnFecha($tareaId, $fechaProgramada)) {
            return ['error' => 'La tarea ya está asignada para esa fecha. Revisa el seguimiento.'];
        }

        $insert = [
            'tarea_id'         => $tareaId,
            'empleado_id'      => $empleadoId,
            'asignado_por'     => $asignadoPor,
            'fecha_programada' => $fechaProgramada,
            'estado'           => EstadoAsignacion::PENDIENTE->value,
        ];

        if ($this->asignaciones->insert($insert) === false) {
            return ['error' => 'No se pudo guardar la asignación.'];
        }

        return ['ok' => true, 'id' => (int) $this->asignaciones->getInsertID()];
    }

    /**
     * Reasigna a otro empleado. Solo si el estado es 'pendiente'.
     */
    public function reasignar(int $asignacionId, int $empleadoId): array
    {
        $asignacion = $this->cargar($asignacionId);

        if ($asignacion['estado'] !== EstadoAsignacion::PENDIENTE->value) {
            return ['error' => 'Solo se pueden reasignar asignaciones pendientes.'];
        }

        $empleado = $this->empleados->find($empleadoId);
        if ($empleado === null || (int) $empleado['activo'] !== 1) {
            return ['error' => 'El empleado seleccionado no existe o no está activo.'];
        }

        if ($this->asignaciones->update($asignacionId, ['empleado_id' => $empleadoId]) === false) {
            return ['error' => 'No se pudo reasignar la tarea.'];
        }

        return ['ok' => true];
    }

    /**
     * Inicia la tarea: pendiente → en_curso y guarda inicio_real.
     *
     * @throws AsignacionException
     */
    public function iniciar(int $asignacionId, int $empleadoId): array
    {
        $asignacion = $this->cargar($asignacionId);
        $this->exigirPropietario($asignacion, $empleadoId);

        if (! EstadoAsignacion::transicionValida($asignacion['estado'], EstadoAsignacion::EN_CURSO->value)) {
            throw new AsignacionException('La asignación no está en estado pendiente para iniciarse.');
        }

        $ahora = date('Y-m-d H:i:s');
        if ($this->asignaciones->update($asignacionId, [
            'estado'     => EstadoAsignacion::EN_CURSO->value,
            'inicio_real' => $ahora,
        ]) === false) {
            throw new AsignacionException('No se pudo iniciar la tarea.');
        }

        return $this->asignaciones->find($asignacionId);
    }

    /**
     * Finaliza la tarea: en_curso → listo, guarda fin_real y crea el registro
     * de limpieza. Toda la escritura va en una transacción.
     *
     * @throws AsignacionException
     */
    public function finalizar(int $asignacionId, int $empleadoId, string $observaciones = ''): array
    {
        $asignacion = $this->cargar($asignacionId);
        $this->exigirPropietario($asignacion, $empleadoId);

        if (! EstadoAsignacion::transicionValida($asignacion['estado'], EstadoAsignacion::LISTO->value)) {
            throw new AsignacionException('La asignación debe estar en curso para finalizarse.');
        }

        $db = db_connect();
        $db->transStart();

        $finReal = date('Y-m-d H:i:s');
        $this->asignaciones->update($asignacionId, [
            'estado'  => EstadoAsignacion::LISTO->value,
            'fin_real' => $finReal,
        ]);

        $inicioReal = $asignacion['inicio_real'];
        $this->registros->insert([
            'asignacion_id' => $asignacionId,
            'tarea_id'      => (int) $asignacion['tarea_id'],
            'empleado_id'   => (int) $asignacion['empleado_id'],
            'fecha'         => date('Y-m-d', strtotime($inicioReal ?: $finReal)),
            'hora_inicio'   => $inicioReal ? date('H:i:s', strtotime($inicioReal)) : null,
            'hora_fin'      => date('H:i:s', strtotime($finReal)),
            'observaciones' => mb_substr(trim($observaciones), 0, 500),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new AsignacionException('No se pudo finalizar la tarea y guardar el registro.');
        }

        return $this->asignaciones->find($asignacionId);
    }

    /**
     * Guarda o actualiza observaciones ligadas a la asignación.
     * Puede usarse al finalizar o después, siempre sobre asignación propia.
     *
     * @throws AsignacionException
     */
    public function registrarObservaciones(int $asignacionId, int $empleadoId, string $observaciones): array
    {
        $asignacion = $this->cargar($asignacionId);
        $this->exigirPropietario($asignacion, $empleadoId);

        $texto  = mb_substr(trim($observaciones), 0, 500);
        $registro = $this->registros->where('asignacion_id', $asignacionId)->first();

        if ($registro === null) {
            if ($texto === '') {
                throw new AsignacionException('La observación es obligatoria.');
            }
            $this->registros->insert([
                'asignacion_id' => $asignacionId,
                'tarea_id'      => (int) $asignacion['tarea_id'],
                'empleado_id'   => (int) $asignacion['empleado_id'],
                'fecha'         => date('Y-m-d'),
                'observaciones' => $texto,
            ]);
        } else {
            $this->registros->update($registro['id'], ['observaciones' => $texto]);
        }

        return ['ok' => true];
    }

    /**
     * Carga la asignación o lanza error 404.
     */
    protected function cargar(int $asignacionId): array
    {
        $asignacion = $this->asignaciones->find($asignacionId);
        if ($asignacion === null) {
            throw new AsignacionException('La asignación no existe.', 404);
        }

        return $asignacion;
    }

    /**
     * La asignación debe pertenecer al empleado de la sesión; si no, 403.
     *
     * @throws AsignacionException
     */
    protected function exigirPropietario(array $asignacion, int $empleadoId): void
    {
        if ((int) $asignacion['empleado_id'] !== $empleadoId) {
            throw new AsignacionException('No tienes permiso para modificar esta asignación.', 403);
        }
    }
}