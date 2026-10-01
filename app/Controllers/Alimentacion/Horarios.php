<?php

namespace App\Controllers\Alimentacion;

use App\Controllers\BaseController;
use App\Models\DietaModel;
use App\Models\HorarioAlimentacionModel;

class Horarios extends BaseController
{
    /**
     * Pantalla principal: formulario de registro + tabla de horarios.
     */
    public function index()
    {
        $horarioModel = new HorarioAlimentacionModel();
        $dietaModel   = new DietaModel();

        return view('alimentacion/horarios', [
            'titulo'   => 'Horarios de alimentación',
            'horarios' => $horarioModel->listaCompleta(),
            'dietas'   => $dietaModel->listaCompleta(),
        ]);
    }

    /**
     * Registra un nuevo horario de alimentación.
     */
    public function guardar()
    {
        $reglas = [
            'dieta_id' => 'required|is_natural_no_zero',
            'hora'     => 'required',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $horarioModel = new HorarioAlimentacionModel();

        $dietaId = $this->request->getPost('dieta_id');
        $hora    = $this->request->getPost('hora');

        $existe = $horarioModel
            ->where('dieta_id', $dietaId)
            ->where('hora', $hora)
            ->first();

        if ($existe) {
            return redirect()->back()->withInput()->with('errores', [
                'horario' => 'Ya existe un horario registrado para esa dieta a esa hora.',
            ]);
        }

        $horarioModel->insert([
            'dieta_id' => $dietaId,
            'hora'     => $hora,
        ]);

        return redirect()->to('/alimentacion/horarios')->with('mensaje', 'Horario registrado correctamente.');
    }

    /**
     * Elimina un horario registrado.
     */
    public function eliminar($id)
    {
        $horarioModel = new HorarioAlimentacionModel();
        $horarioModel->delete($id);

        return redirect()->to('/alimentacion/horarios')->with('mensaje', 'Horario eliminado correctamente.');
    }
}