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
 *
 * Adaptado al modelo E-R real del proyecto: no existen tablas independientes
 * "ventas"/"boletos", así que cada boleto es una fila de entradas.entradas
 * con su propio pago en entradas.pagos. No se agrupan boletos por venta.
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
        $resumen = $db->table('entradas.entradas as e')
            ->select('COUNT(*) as total_boletos, COALESCE(SUM(e.total),0) as monto')
            ->join('entradas.pagos as p', 'p.entrada_id = e.id', 'left')
            ->where('e.fecha_compra >=', $fecha . ' 00:00:00')
            ->where('(p.estado IS NULL OR p.estado != \'rechazado\')', null, false)
            ->get()->getResultArray();

        $totalBoletos = (int) ($resumen[0]['total_boletos'] ?? 0);
        $monto        = (float) ($resumen[0]['monto'] ?? 0);

        $ventas = $ventaModel->reporte($desde, $fecha);

        return view('entradas/punto_venta/index', [
            'titulo'     => 'Taquilla',
            'cssExtra'   => 'entradas.css',
            'ventas'     => $ventas,
            'resumenHoy' => ['ventas' => $totalBoletos, 'monto' => $monto, 'boletos' => $totalBoletos],
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

        $visitanteModel = model(VisitanteModel::class);
        $clienteNombre  = trim((string) $this->request->getPost('cliente_nombre'));
        $clienteEmail   = trim((string) $this->request->getPost('cliente_email'));

        if ($clienteNombre !== '') {
            $cliente = $clienteEmail !== '' ? $visitanteModel->buscarPorEmail($clienteEmail) : null;
            if ($cliente === null) {
                $visitanteModel->insert([
                    'nombre'   => $clienteNombre,
                    'email'    => $clienteEmail !== '' ? $clienteEmail : null,
                    'telefono' => trim((string) $this->request->getPost('cliente_telefono')),
                ]);
                if ($visitanteModel->errors() !== []) {
                    return redirect()->back()->with('error', implode(' ', $visitanteModel->errors()));
                }
                $clienteId = (int) $visitanteModel->getInsertID();
            } else {
                $clienteId = (int) $cliente['id'];
            }
        } else {
            // entradas.entradas.visitante_id es obligatorio (NOT NULL) en el
            // modelo E-R: para una venta sin datos de cliente, reutilizamos
            // (o creamos una sola vez) un visitante genérico de taquilla.
            $generico = $visitanteModel->where('email', 'taquilla.general@mirada-salvaje.local')->first();
            if ($generico === null) {
                $visitanteModel->insert([
                    'nombre'   => 'Visitante de taquilla (sin registrar)',
                    'email'    => 'taquilla.general@mirada-salvaje.local',
                    'telefono' => null,
                ]);
                $clienteId = (int) $visitanteModel->getInsertID();
            } else {
                $clienteId = (int) $generico['id'];
            }
        }

        try {
            $servicio = new ServicioVentas();
            $resultado = $servicio->crearVenta($lineas, [
                'cliente_id'   => $clienteId,
                'fecha_visita' => (string) $this->request->getPost('fecha_visita'),
                'punto_venta'  => (string) $this->request->getPost('punto_venta') ?: 'Taquilla principal',
                'metodo_pago'  => (string) $this->request->getPost('metodo_pago'),
            ]);
        } catch (VentaException $ex) {
            return redirect()->back()->with('error', $ex->getMessage());
        }

        return redirect()->to('/entradas/taquilla/detalle/' . $resultado['primer_id'])
            ->with('success', 'Se registraron ' . $resultado['cantidad'] . ' boleto(s) por un total de Q ' . number_format($resultado['total'], 2) . '.');
    }

    public function detalle(int $id): string
    {
        $venta = model(VentaModel::class)->conDetalle($id);
        if ($venta === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('entradas/punto_venta/detalle', [
            'titulo'   => 'Boleto ' . $venta['codigo'],
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

        return redirect()->to('/entradas/taquilla/detalle/' . $id)->with('success', 'Boleto anulado correctamente.');
    }

    private function promosPorTarifa(): array
    {
        $promos = model(PromocionModel::class)->findAll();
        $mapa   = [];

        // No existe tabla promocion_tarifa en el E-R actual: toda promoción
        // vigente se ofrece para todas las tarifas.
        $hoy = date('Y-m-d');
        foreach ($promos as $promo) {
            if ($promo['fecha_inicio'] > $hoy || $promo['fecha_fin'] < $hoy) {
                continue;
            }
            foreach (model(TarifaModel::class)->activas() as $tarifa) {
                $mapa[(int) $tarifa['id']][] = $promo;
            }
        }

        return $mapa;
    }
}