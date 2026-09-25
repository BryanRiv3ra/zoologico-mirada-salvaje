<?php

namespace App\Controllers\Alimentacion;

use App\Controllers\BaseController;
use App\Models\AnimalModel;
use App\Models\DietaModel;

class Dietas extends BaseController
{
    /**
     * Pantalla principal: formulario de registro + tabla de dietas.
     */
    public function index()
    {
        $dietaModel  = new DietaModel();
        $animalModel = new AnimalModel();

        return view('alimentacion/dietas', [
            'titulo'   => 'Dietas por animal',
            'dietas'   => $dietaModel->listaCompleta(),
            'animales' => $animalModel->listaActivos(),
        ]);
    }

    /**
     * Registra una nueva dieta para un animal.
     */
    public function guardar()
    {
        $reglas = [
            'animal_id'   => 'required|is_natural_no_zero',
            'descripcion' => 'required|min_length[3]',
            'frecuencia'  => 'required|max_length[100]',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()
                ->withInput()
                ->with('errores', $this->validator->getErrors());
        }

        $dietaModel = new DietaModel();

        $dietaModel->insert([
            'animal_id'   => $this->request->getPost('animal_id'),
            'descripcion' => $this->request->getPost('descripcion'),
            'frecuencia'  => $this->request->getPost('frecuencia'),
        ]);

        return redirect()->to('/alimentacion/dietas')
            ->with('mensaje', 'Dieta registrada correctamente.');
    }

    /**
     * Elimina una dieta registrada.
     */
    public function eliminar($id)
    {
        $dietaModel = new DietaModel();
        $dietaModel->delete($id);

        return redirect()->to('/alimentacion/dietas')
            ->with('mensaje', 'Dieta eliminada correctamente.');
    }
}