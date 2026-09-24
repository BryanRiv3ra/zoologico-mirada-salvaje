<?php

namespace App\Controllers;

use App\Models\AnimalModel;
use App\Models\EmpleadoModel;
use App\Models\InventarioModel;
use App\Models\HistorialClinicoModel;
use App\Models\TratamientoModel;
use App\Models\MovimientoInventarioModel;

class Clinico extends BaseController
{
    /**
     * Pantalla principal: formulario de registro + tabla de tratamientos.
     */
    public function index()
    {
        $tratamientoModel = new TratamientoModel();
        $animalModel      = new AnimalModel();
        $empleadoModel    = new EmpleadoModel();
        $inventarioModel  = new InventarioModel();

        return view('clinico/index', [
            'titulo'      => 'Control clínico',
            'tratamientos'=> $tratamientoModel->listaCompleta(),
            'animales'    => $animalModel->listaActivos(),
            'empleados'   => $empleadoModel->listaActivos(),
            'insumos'     => $inventarioModel->listaMedicamentosYVitaminas(),
        ]);
    }

    /**
     * Registra un nuevo tratamiento. Como un tratamiento nunca existe sin
     * un diagnóstico (regla de negocio del módulo), este método crea AMBOS
     * registros -historial_clinico y tratamientos- en una sola transacción,
     * y además descuenta el insumo del inventario (relación <<incluye>>
     * del diagrama de casos de uso).
     */
    public function guardar()
    {
        $reglas = [
            'animal_id'      => 'required|is_natural_no_zero',
            'veterinario_id' => 'required|is_natural_no_zero',
            'diagnostico'    => 'required|min_length[3]',
            'inventario_id'  => 'required|is_natural_no_zero',
            'dosis'          => 'required|max_length[100]',
            'frecuencia'     => 'permit_empty|max_length[100]',
            'cantidad'       => 'required|decimal|greater_than[0]',
            'fecha_inicio'   => 'required|valid_date',
            'fecha_fin'      => 'permit_empty|valid_date',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $historialModel   = new HistorialClinicoModel();
        $tratamientoModel = new TratamientoModel();
        $inventarioModel  = new InventarioModel();
        $movimientoModel  = new MovimientoInventarioModel();

        // Verifica que haya stock suficiente antes de continuar
        $insumo = $inventarioModel->find($this->request->getPost('inventario_id'));
        $cantidad = (float) $this->request->getPost('cantidad');

        if (! $insumo || $insumo['stock_actual'] < $cantidad) {
            return redirect()->back()->withInput()
                ->with('errores', ['stock' => 'No hay suficiente stock de este insumo. Disponible: ' . ($insumo['stock_actual'] ?? 0) . ' ' . ($insumo['unidad'] ?? '')]);
        }

        // 1. Diagnóstico (historial clínico)
        $historialId = $historialModel->insert([
            'animal_id'      => $this->request->getPost('animal_id'),
            'diagnostico'    => $this->request->getPost('diagnostico'),
            'veterinario_id' => $this->request->getPost('veterinario_id'),
            'fecha'          => date('Y-m-d H:i:s'),
        ]);

        // 2. Tratamiento derivado de ese diagnóstico
        $tratamientoModel->insert([
            'historial_id'  => $historialId,
            'inventario_id' => $this->request->getPost('inventario_id'),
            'dosis'         => $this->request->getPost('dosis'),
            'frecuencia'    => $this->request->getPost('frecuencia'),
            'fecha_inicio'  => $this->request->getPost('fecha_inicio'),
            'fecha_fin'     => $this->request->getPost('fecha_fin') ?: null,
        ]);

        // 3. Descuenta inventario (caso de uso incluido)
        $inventarioModel->descontarStock((int) $this->request->getPost('inventario_id'), $cantidad);

        // 4. Deja registro del movimiento, para trazabilidad
        $movimientoModel->insert([
            'inventario_id'   => $this->request->getPost('inventario_id'),
            'tipo_movimiento' => 'salida',
            'cantidad'        => $cantidad,
            'fecha'           => date('Y-m-d H:i:s'),
            'motivo'          => 'Tratamiento clínico aplicado',
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('errores', ['general' => 'No se pudo registrar el tratamiento. Intenta de nuevo.']);
        }

        return redirect()->to('/clinico')->with('mensaje', 'Tratamiento registrado correctamente.');
    }

    /**
     * Formulario de edición de un tratamiento existente. Solo permite
     * ajustar dosis, frecuencia y fecha de fin (por ejemplo, para cerrar
     * un tratamiento) — no se reasigna el animal ni el insumo, para no
     * romper la trazabilidad del inventario ya descontado.
     */
    public function editar($id)
    {
        $tratamientoModel = new TratamientoModel();
        $tratamiento = $tratamientoModel->find($id);

        if (! $tratamiento) {
            return redirect()->to('/clinico')->with('errores', ['general' => 'Tratamiento no encontrado.']);
        }

        return view('clinico/editar', [
            'titulo'      => 'Editar tratamiento',
            'tratamiento' => $tratamiento,
        ]);
    }

    public function actualizar($id)
    {
        $reglas = [
            'dosis'      => 'required|max_length[100]',
            'frecuencia' => 'permit_empty|max_length[100]',
            'fecha_fin'  => 'permit_empty|valid_date',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $tratamientoModel = new TratamientoModel();
        $tratamientoModel->update($id, [
            'dosis'      => $this->request->getPost('dosis'),
            'frecuencia' => $this->request->getPost('frecuencia'),
            'fecha_fin'  => $this->request->getPost('fecha_fin') ?: null,
        ]);

        return redirect()->to('/clinico')->with('mensaje', 'Tratamiento actualizado correctamente.');
    }

    /**
     * Elimina un tratamiento. No revierte el stock automáticamente:
     * el insumo ya fue consumido físicamente por el animal.
     */
    public function eliminar($id)
    {
        $tratamientoModel = new TratamientoModel();
        $tratamientoModel->delete($id);

        return redirect()->to('/clinico')->with('mensaje', 'Tratamiento eliminado.');
    }
}
