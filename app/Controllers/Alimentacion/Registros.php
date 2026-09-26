<?php

namespace App\Controllers\Alimentacion;

use App\Controllers\BaseController;
use App\Models\EmpleadoModel;
use App\Models\HorarioAlimentacionModel;
use App\Models\RegistroAlimentacionModel;

class Registros extends BaseController
{
    /**
     * Pantalla principal: formulario de registro + tabla de alimentaciones realizadas.
     */
    public function index()
    {
        $registroModel = new RegistroAlimentacionModel();
        $horarioModel  = new HorarioAlimentacionModel();
        $empleadoModel = new EmpleadoModel();

        return view('alimentacion/registros', [
            'titulo'    => 'Registros de alimentación',
            'registros' => $registroModel->listaCompleta(),
            'horarios'  => $horarioModel->listaCompleta(),
            'empleados' => $empleadoModel->listaActivos(),
        ]);
    }

    /**
     * Registra una alimentación realizada.
     */
    public function guardar()
    {
        $reglas = [
            'horario_id'  => 'required|is_natural_no_zero',
            'empleado_id' => 'required|is_natural_no_zero',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $registroModel = new RegistroAlimentacionModel();

        $registroModel->insert([
            'horario_id'    => $this->request->getPost('horario_id'),
            'empleado_id'   => $this->request->getPost('empleado_id'),
            'observaciones' => $this->request->getPost('observaciones'),
        ]);

        return redirect()->to('/alimentacion/registros')->with('mensaje', 'Registro de alimentación guardado correctamente.');
    }

    /**
     * Elimina un registro de alimentación.
     */
    public function eliminar($id)
    {
        $registroModel = new RegistroAlimentacionModel();
        $registroModel->delete($id);

        return redirect()->to('/alimentacion/registros')->with('mensaje', 'Registro eliminado correctamente.');
    }
}