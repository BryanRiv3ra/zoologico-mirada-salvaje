<?php

namespace App\Controllers\Entradas;

use App\Controllers\BaseController;
use App\Libraries\Entradas\GeneradorQr;
use App\Libraries\Entradas\ServicioVentas;
use App\Libraries\Entradas\VentaException;
use App\Models\Entradas\PromocionModel;
use App\Models\Entradas\PromocionTarifaModel;
use App\Models\Entradas\TarifaModel;
use App\Models\Entradas\VentaModel;
use App\Models\Entradas\VisitanteModel;

/**
 * Punto de venta (taquilla). Permiso: administrador, cajero, supervisor.
 */
class PuntoVenta extends BaseController
{
    protected $helpers = ['url', 'form'];

    public function index(): string
    {
        $ventaModel = model(VentaModel::class);
        $fecha = date('Y-m-d');
        $desde = date('Y-m-d', strtotime('-30 days'));

        $db = db_connect();
        $hoy = (array) $db->table('entradas.ventas')
            ->select('COUNT(*) as total_ventas, COALESCE(SUM(total),0) as monto')
            ->where('estado', 'completada')
            ->where('fecha >=', $fecha . ' 00:00:00')
            ->get()->getResultArray();

        $boletosHoy = (int) $db->table('entradas.boletos as b')
            ->join('entradas.ventas as v', 'v.id = b.venta_id')
            ->where('v.estado', 'completada')
            ->where('v.fecha >=', $fecha . ' 00:00:00')
            ->countAllResults();

        $ventas = $ventaModel->reporte($desde, $fecha);

        return view('entradas/punto_venta/index', [
            'titulo'      => 'Taquilla',
            'cssExtra'    => 'entradas.css',
            'ventas'      => $ventas,
            'resumenHoy'  => ['ventas' => (int) ($hoy[0]['total_ventas'] ?? 0), 'monto' => (float) ($hoy[0]['monto'] ?? 0), 'boletos' => $boletosHoy],
        ]);
    }

    public function nueva(): string
    {
        return view('entradas/punto_venta/nueva', [
            'titulo'          => 'Nueva venta · Taquilla',
            'cssExtra'        => 'entradas.css',
            'tarifas'         => model(TarifaModel::class)->activas(),
            'promosPorTarifa' => $this->promosPorTarifa(),
            'fechaVisita'     => date('Y-m-d'),
            'metodosPago'     => ['efectivo', 'tarjeta', 'transferencia'],
        ]);
    }

    public function guardar()
    {
        $cantidades = (array) $this->request->getPost('cantidad');
        $promoSel   = (array) $this->request->getPost('promo');

        $lineas = [];
        foreach ($cantidades as $tarifaId => $cantidad) {
            $cantidad = (int) $cantidad;
            if ($cantidad < 1) {
                continue;
            }
            $promoId  = isset($promoSel[$tarifaId]) ? (int) $promoSel[$tarifaId] : 0;
            $lineas[] = [
                'tarifa_id' => (int) $tarifaId,
                'cantidad'  => $cantidad,
                'promo_id'  => $promoId > 0 ? $promoId : null,
            ];
        }

        if ($lineas === []) {
            return redirect()->back()->with('error', 'Elige al menos una entrada.');
        }

        $empleadoId = (int) session('empleado_id');
        if ($empleadoId <= 0) {
            return redirect()->back()->with('error', 'No hay un empleado de taquilla asociado a la sesión.');
        }

        $clienteId = null;
        $clienteNombre = trim((string) $this->request->getPost('cliente_nombre'));
        $clienteEmail  = trim((string) $this->request->getPost('cliente_email'));

        if ($clienteNombre !== '') {
            $visitanteModel = model(VisitanteModel::class);
            $cliente = $clienteEmail !== '' ? $visitanteModel->buscarPorEmail($clienteEmail) : null;
            if ($cliente === null) {
                $visitanteModel->insert([
                    'nombre'   => $clienteNombre,
                    'email'    => $clienteEmail,
                    'telefono' => trim((string) $this->request->getPost('cliente_telefono')),
                ]);
                if ($visitanteModel->errors() !== []) {
                    return redirect()->back()->with('error', implode(' ', $visitanteModel->errors()));
                }
                $clienteId = (int) $visitanteModel->getInsertID();
            } else {
                $clienteId = (int) $cliente['id'];
            }
        }

        try {
            $servicio = new ServicioVentas();
            $venta = $servicio->crearVenta($lineas, [
                'empleado_id'  => $empleadoId,
                'cliente_id'   => $clienteId,
                'fecha_visita' => (string) $this->request->getPost('fecha_visita'),
                'tipo_venta'   => 'taquilla',
                'punto_venta'  => (string) $this->request->getPost('punto_venta') ?? 'Taquilla principal',
                'metodo_pago'  => (string) $this->request->getPost('metodo_pago'),
            ]);
        } catch (VentaException $ex) {
            return redirect()->back()->with('error', $ex->getMessage());
        }

        return redirect()->to('/entradas/taquilla/detalle/' . $venta['venta_id'])
            ->with('success', 'Venta ' . $venta['codigo_venta'] . ' registrada correctamente.');
    }

    public function detalle(int $id): string
    {
        $venta = model(VentaModel::class)->conDetalle($id);
        if ($venta === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('entradas/punto_venta/detalle', [
            'titulo'   => 'Venta ' . $venta['codigo'],
            'cssExtra' => 'entradas.css',
            'venta'    => $venta,
            'qr'       => new GeneradorQr(),
        ]);
    }

    public function anular(int $id)
    {
        $motivo = (string) $this->request->getPost('motivo');
        $empleadoId = (int) session('empleado_id');

        try {
            (new ServicioVentas())->anularVenta($id, $motivo, $empleadoId);
        } catch (VentaException $ex) {
            return redirect()->back()->with('error', $ex->getMessage());
        }

        return redirect()->to('/entradas/taquilla/detalle/' . $id)->with('success', 'Venta anulada correctamente.');
    }

    private function promosPorTarifa(): array
    {
        $promos = model(PromocionModel::class)->activas();
        $mapa   = [];

        foreach ($promos as $promo) {
            $relaciones = model(PromocionTarifaModel::class)
                ->select('tarifa_id')
                ->where('promocion_id', $promo['id'])
                ->findAll();

            foreach ($relaciones as $relacion) {
                $mapa[(int) $relacion['tarifa_id']][] = $promo;
            }
        }

        return $mapa;
    }
}