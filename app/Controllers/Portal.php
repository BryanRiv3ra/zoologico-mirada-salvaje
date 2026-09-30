<?php

namespace App\Controllers;

use App\Libraries\Entradas\GeneradorQr;
use App\Libraries\Entradas\ServicioVentas;
use App\Libraries\Entradas\VentaException;
use App\Models\Entradas\PromocionModel;
use App\Models\Entradas\TarifaModel;
use App\Models\Entradas\VentaModel;
use App\Models\Entradas\VisitanteModel;

/**
 * Portal público de compra de entradas.
 * No requiere autenticación (solo protección CSRF en POST).
 *
 * Adaptado al modelo E-R real: no existe la tabla `entradas.promocion_tarifa`,
 * asi que toda promoción vigente aplica a todas las tarifas activas (misma
 * regla que Entradas\PuntoVenta::promosPorTarifa()).
 */
class Portal extends BaseController
{
    protected $helpers = ['url', 'form'];

    private TarifaModel $tarifas;
    private PromocionModel $promociones;

    public function __construct()
    {
        $this->tarifas     = model(TarifaModel::class);
        $this->promociones = model(PromocionModel::class);
    }

    public function index(): string
    {
        $tarifas     = $this->tarifas->activas();
        $promociones = $this->promociones->activas();

        // Sin tabla de relación: cada promoción aplica a todas las tarifas activas.
        $idsTarifas = array_map(static fn (array $t): int => (int) $t['id'], $tarifas);

        $promosDetalle = [];
        foreach ($promociones as $promo) {
            $promosDetalle[] = [
                'promocion'    => $promo,
                'tarifa_count' => count($idsTarifas),
                'tarifas'      => $idsTarifas,
            ];
        }

        return view('portal/index', [
            'titulo'     => 'Compra de Entradas',
            'tarifas'    => $tarifas,
            'promos'     => $promosDetalle,
            'cssExtra'   => 'entradas.css',
        ]);
    }

    public function comprar(): string
    {
        $tarifas         = $this->tarifas->activas();
        $promosPorTarifa = $this->promosPorTarifa($tarifas);

        return view('portal/comprar', [
            'titulo'          => 'Comprar Entradas',
            'tarifas'         => $tarifas,
            'promosPorTarifa' => $promosPorTarifa,
            'cssExtra'        => 'entradas.css',
            'fechaVisita'     => date('Y-m-d'),
        ]);
    }

    public function confirmar()
    {
        $cantidades = (array) $this->request->getPost('cantidad');
        $promoSeleccionada = (array) $this->request->getPost('promo');
        $fechaVisita = trim((string) $this->request->getPost('fecha_visita'));
        $nombre  = trim((string) $this->request->getPost('nombre'));
        $email   = trim((string) $this->request->getPost('email'));
        $telefono = trim((string) $this->request->getPost('telefono'));

        if ($nombre === '') {
            return redirect()->to('/portal/comprar')->with('error', 'Debes indicar tu nombre.');
        }
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->to('/portal/comprar')->with('error', 'Debes indicar un correo electrónico válido.');
        }

        $lineas = [];
        foreach ($cantidades as $tarifaId => $cantidad) {
            $cantidad = (int) $cantidad;
            if ($cantidad < 1) {
                continue;
            }

            $promoId = isset($promoSeleccionada[$tarifaId]) ? (int) $promoSeleccionada[$tarifaId] : 0;
            $lineas[] = [
                'tarifa_id' => (int) $tarifaId,
                'cantidad'  => $cantidad,
                'promo_id'  => $promoId > 0 ? $promoId : null,
            ];
        }

        if ($lineas === []) {
            return redirect()->to('/portal/comprar')->with('error', 'Elige al menos una entrada.');
        }

        try {
            $visitanteModel = model(VisitanteModel::class);
            $cliente = $visitanteModel->buscarPorEmail($email);
            if ($cliente === null) {
                $visitanteModel->insert([
                    'nombre'   => $nombre,
                    'email'    => $email,
                    'telefono' => $telefono,
                ]);
                if ($visitanteModel->errors() !== []) {
                    throw new VentaException(implode(' ', $visitanteModel->errors()));
                }
                $clienteId = (int) $visitanteModel->getInsertID();
            } else {
                $clienteId = (int) $cliente['id'];
            }

            $servicio = new ServicioVentas();
            $venta = $servicio->crearVenta($lineas, [
                'empleado_id' => 0,
                'cliente_id'  => $clienteId,
                'fecha_visita' => $fechaVisita,
                'tipo_venta'  => 'portal',
                'punto_venta' => 'Portal web',
                'metodo_pago' => 'tarjeta',
            ]);
        } catch (VentaException $ex) {
            return redirect()->to('/portal/comprar')->with('error', $ex->getMessage());
        }

        session()->set('ultima_venta', $venta['venta_id']);

        return redirect()->to('/portal/ticket/' . $venta['venta_id'])->with('success', 'Pago aprobado. Tu comprobante está listo.');
    }

    public function ticket(int $id): string
    {
        $venta = model(VentaModel::class)->conDetalle($id);
        if ($venta === null || $venta['tipo_venta'] !== 'portal') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($venta['estado'] !== 'completada') {
            return redirect()->to('/portal')->with('error', 'Este comprobante fue anulado.');
        }

        $qr = new GeneradorQr();

        return view('portal/ticket', [
            'titulo'   => 'Comprobante ' . $venta['codigo'],
            'venta'    => $venta,
            'qr'       => $qr,
            'cssExtra' => 'entradas.css',
        ]);
    }

    /**
     * Mapa [tarifa_id => [promociones...]].
     *
     * No existe tabla `entradas.promocion_tarifa` en el E-R actual, asi que
     * toda promoción vigente se ofrece para todas las tarifas activas.
     *
     * @param list<array<string,mixed>> $tarifas Tarifas ya activas.
     * @return array<int, list<array<string,mixed>>>
     */
    private function promosPorTarifa(array $tarifas): array
    {
        $mapa = [];

        foreach ($this->promociones->activas() as $promo) {
            foreach ($tarifas as $tarifa) {
                $mapa[(int) $tarifa['id']][] = $promo;
            }
        }

        return $mapa;
    }
}