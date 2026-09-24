<?php

namespace App\Controllers;

use App\Models\AnimalModel;
use App\Models\EmpleadoModel;
use App\Models\VacunaModel;
use App\Models\AplicacionVacunaModel;

class ClinicoVacunas extends BaseController
{
    /**
     * Calendario de vacunas: catálogo + formulario de aplicación +
     * listado dividido en "Próximas" y "Aplicadas".
     */
    public function index()
    {
        $animalModel      = new AnimalModel();
        $empleadoModel    = new EmpleadoModel();
        $vacunaModel      = new VacunaModel();
        $aplicacionModel  = new AplicacionVacunaModel();

        return view('clinico/vacunas', [
            'titulo'     => 'Calendario de vacunas',
            'animales'   => $animalModel->listaActivos(),
            'empleados'  => $empleadoModel->listaActivos(),
            'vacunas'    => $vacunaModel->listaTodas(),
            'proximas'   => $aplicacionModel->proximas(),
            'aplicadas'  => $aplicacionModel->aplicadas(),
        ]);
    }

    /**
     * Agrega una vacuna nueva al catálogo (clinico.vacunas), por si el
     * zoológico incorpora una vacuna que todavía no existe en el sistema.
     */
    public function guardarVacuna()
    {
        $reglas = [
            'nombre'      => 'required|min_length[2]|max_length[100]',
            'descripcion' => 'permit_empty|max_length[255]',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->to('/clinico/vacunas')->withInput()->with('errores', $this->validator->getErrors());
        }

        $vacunaModel = new VacunaModel();
        $vacunaModel->insert([
            'nombre'      => $this->request->getPost('nombre'),
            'descripcion' => $this->request->getPost('descripcion'),
        ]);

        return redirect()->to('/clinico/vacunas')->with('mensaje', 'Vacuna agregada al catálogo.');
    }

    /**
     * Registra la aplicación de una vacuna a un animal. A diferencia de
     * un tratamiento, NO requiere diagnóstico previo: vacunar es una
     * acción preventiva (regla de negocio definida en el caso de uso
     * "Registrar vacuna"), por lo que aquí solo se inserta en
     * clinico.aplicaciones_vacunas — sin tocar clinico.historial_clinico.
     */
    public function guardar()
    {
        $reglas = [
            'animal_id'      => 'required|is_natural_no_zero',
            'vacuna_id'      => 'required|is_natural_no_zero',
            'veterinario_id' => 'required|is_natural_no_zero',
            'fecha'          => 'required|valid_date',
            'dosis'          => 'permit_empty|max_length[100]',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->to('/clinico/vacunas')->withInput()->with('errores', $this->validator->getErrors());
        }

        $aplicacionModel = new AplicacionVacunaModel();

        $animalId = (int) $this->request->getPost('animal_id');
        $vacunaId = (int) $this->request->getPost('vacuna_id');
        $fecha    = $this->request->getPost('fecha');

        if ($aplicacionModel->yaExiste($animalId, $vacunaId, $fecha)) {
            return redirect()->to('/clinico/vacunas')->withInput()
                ->with('errores', ['duplicado' => 'Ya existe un registro de esta vacuna para este animal en esa fecha.']);
        }

        $aplicacionModel->insert([
            'animal_id'      => $animalId,
            'vacuna_id'      => $vacunaId,
            'veterinario_id' => (int) $this->request->getPost('veterinario_id'),
            'fecha'          => $fecha,
            'dosis'          => $this->request->getPost('dosis'),
        ]);

        return redirect()->to('/clinico/vacunas')->with('mensaje', 'Vacuna registrada correctamente en el calendario.');
    }

    /**
     * Elimina un registro de aplicación de vacuna (por ejemplo, si se
     * capturó por error o se reprogramó la fecha).
     */
    public function eliminar($id)
    {
        $aplicacionModel = new AplicacionVacunaModel();
        $aplicacionModel->delete($id);

        return redirect()->to('/clinico/vacunas')->with('mensaje', 'Registro de vacuna eliminado.');
    }
}
