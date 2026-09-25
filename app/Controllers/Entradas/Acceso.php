<?php

namespace App\Controllers\Entradas;

use App\Controllers\BaseController;
use App\Models\Entradas\BoletoModel;

/**
 * Control de ingreso: valida el QR/código del boleto en la puerta.
 * Permiso: control_acceso, administrador.
 */
class Acceso extends BaseController
{
    protected $helpers = ['url', 'form'];

    public function index(): string
    {
        $codigo = trim((string) $this->request->getGet('q'));
        $boleto = null;

        if ($codigo !== '') {
            $boleto = model(BoletoModel::class)->porCodigo($codigo);
        }

        return view('entradas/acceso/index', [
            'titulo'   => 'Control de acceso',
            'cssExtra' => 'entradas.css',
            'codigo'   => $codigo,
            'boleto'   => $boleto,
        ]);
    }

    public function buscar()
    {
        return redirect()->to('/entradas/acceso?q=' . urlencode((string) $this->request->getGet('q')));
    }

    public function validar(int $id)
    {
        $boleto = model(BoletoModel::class)->find($id);
        if ($boleto === null) {
            return redirect()->to('/entradas/acceso')->with('error', 'El boleto no existe.');
        }

        if ($boleto['estado'] === 'emitido') {
            model(BoletoModel::class)->marcarUsado($id);

            return redirect()->to('/entradas/acceso?q=' . urlencode($boleto['codigo_qr']))
                ->with('success', 'Ingreso validado correctamente.');
        }

        $mensajes = [
            'usado'   => 'Este boleto ya fue utilizado. Acceso rechazado.',
            'anulado' => 'Este boleto pertenece a una venta anulada. Acceso rechazado.',
        ];

        return redirect()->to('/entradas/acceso?q=' . urlencode($boleto['codigo_qr']))
            ->with('error', $mensajes[$boleto['estado']] ?? 'Estado de boleto no válido.');
    }
}