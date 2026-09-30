<?php

namespace App\Controllers\Alimentacion;

use App\Controllers\BaseController;
use App\Models\InventarioModel;

class Inventario extends BaseController
{
    /**
     * Pantalla principal: consulta y registro de inventario de alimentos.
     */
    public function index()
    {
        $inventarioModel = new InventarioModel();

        return view('alimentacion/inventario', [
            'titulo'    => 'Inventario de alimentos',
            'alimentos' => $inventarioModel->listaAlimentos(),
            'stockBajo' => $inventarioModel->alimentosConStockBajo(),
        ]);
    }

    /**
     * Registra un nuevo alimento en inventario.
     */
    public function guardar()
    {
        $reglas = [
            'nombre'       => 'required|min_length[3]|max_length[150]',
            'unidad'       => 'required|max_length[30]',
            'stock_actual' => 'required|numeric',
            'stock_minimo' => 'required|numeric',
        ];

        if (! $this->validate($reglas)) {
            return redirect()->back()->withInput()->with('errores', $this->validator->getErrors());
        }

        $inventarioModel = new InventarioModel();

        $nombre = trim($this->request->getPost('nombre'));

        $existe = $inventarioModel
            ->where('nombre', $nombre)
            ->first();

        if ($existe) {
            return redirect()->back()->withInput()->with('errores', [
                'nombre' => 'Ya existe un producto con ese nombre en el inventario.',
            ]);
        }

        $inventarioModel->insert([
            'nombre'            => $nombre,
            'tipo'              => 'alimento',
            'unidad'            => $this->request->getPost('unidad'),
            'stock_actual'      => $this->request->getPost('stock_actual'),
            'stock_minimo'      => $this->request->getPost('stock_minimo'),
            'fecha_vencimiento' => $this->request->getPost('fecha_vencimiento') ?: null,
        ]);

        return redirect()->to('/alimentacion/inventario')->with('mensaje', 'Alimento registrado correctamente.');
    }

    /**
     * Elimina un alimento del inventario.
     */
    public function eliminar($id)
    {
        $inventarioModel = new InventarioModel();
        $inventarioModel->delete($id);

        return redirect()->to('/alimentacion/inventario')->with('mensaje', 'Alimento eliminado correctamente.');
    }
}