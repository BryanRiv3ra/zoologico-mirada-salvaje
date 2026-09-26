<?php

namespace App\Controllers\Alimentacion;

use App\Controllers\BaseController;
use App\Models\InventarioModel;
use App\Models\RegistroAlimentacionModel;

class Reportes extends BaseController
{
    /**
     * Reporte general del módulo de alimentación.
     */
    public function index()
    {
        $inventarioModel = new InventarioModel();
        $registroModel   = new RegistroAlimentacionModel();

        $alimentos = $inventarioModel->listaAlimentos();
        $stockBajo = $inventarioModel->alimentosConStockBajo();
        $registros = $registroModel->listaCompleta();

        return view('alimentacion/reportes', [
            'titulo'         => 'Reporte de alimentación',
            'alimentos'      => $alimentos,
            'stockBajo'      => $stockBajo,
            'registros'      => $registros,
            'totalAlimentos' => count($alimentos),
            'totalStockBajo' => count($stockBajo),
            'totalRegistros' => count($registros),
        ]);
    }
}