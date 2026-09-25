<?php

namespace App\Libraries\Entradas;

use App\Models\Entradas\BoletoModel;
use App\Models\Entradas\PagoModel;
use App\Models\Entradas\PromocionModel;
use App\Models\Entradas\PromocionTarifaModel;
use App\Models\Entradas\TarifaModel;
use App\Models\Entradas\VentaModel;

/**
 * Lógica de negocio de ventas: emisión de venta con boletos y pago,
 * y anulación de ventas. Opera en transacción.
 */
class ServicioVentas
{
    private TarifaModel $tarifaModel;
    private PromocionModel $promocionModel;
    private PromocionTarifaModel $promocionTarifaModel;
    private VentaModel $ventaModel;
    private BoletoModel $boletoModel;
    private PagoModel $pagoModel;
    private CalculadoraVenta $calculadora;
    private GeneradorQr $generadorQr;
    private PasarelaPagoSimulada $pasarela;

    public function __construct()
    {
        $this->tarifaModel          = new TarifaModel();
        $this->promocionModel       = new PromocionModel();
        $this->promocionTarifaModel = new PromocionTarifaModel();
        $this->ventaModel           = new VentaModel();
        $this->boletoModel          = new BoletoModel();
        $this->pagoModel            = new PagoModel();
        $this->calculadora          = new CalculadoraVenta();
        $this->generadorQr          = new GeneradorQr();
        $this->pasarela             = new PasarelaPagoSimulada();
    }

    /**
     * @param list<array{tarifa_id:int, cantidad:int, promo_id:int|null}> $lineas
     * @param array{empleado_id:int, cliente_id:int|null, fecha_visita:string, tipo_venta:string, punto_venta:string, metodo_pago:string} $datos
     */
    public function crearVenta(array $lineas, array $datos): array
    {
        if ($lineas === []) {
            throw new VentaException('Debes agregar al menos una entrada a la venta.');
        }

        $fechaVisita = $datos['fecha_visita'];
        if ($fechaVisita === '' || $fechaVisita < date('Y-m-d')) {
            throw new VentaException('La fecha de visita debe ser hoy o una fecha futura.');
        }
        if ($fechaVisita > date('Y-m-d', strtotime('+1 year'))) {
            throw new VentaException('La fecha de visita no puede superar un año de anticipación.');
        }

        $tarifas = $this->tarifaModel->activas();
        $mapaTarifas = [];
        foreach ($tarifas as $tarifa) {
            $mapaTarifas[(int) $tarifa['id']] = $tarifa;
        }

        $items = [];
        foreach ($lineas as $linea) {
            $tarifaId = (int) ($linea['tarifa_id'] ?? 0);
            $promoId  = (int) ($linea['promo_id'] ?? 0);

            if (! isset($mapaTarifas[$tarifaId])) {
                throw new VentaException('Se eligió una tarifa no disponible.');
            }

            $descuento = 0.0;
            if ($promoId > 0) {
                $promo = $this->validarPromoAplicable($promoId, $tarifaId);
                $descuento = (float) $promo['descuento'];
            }

            $items[] = [
                'tarifa_id' => $tarifaId,
                'nombre'    => $mapaTarifas[$tarifaId]['nombre'],
                'precio'    => (float) $mapaTarifas[$tarifaId]['precio'],
                'cantidad'  => max(1, (int) ($linea['cantidad'] ?? 1)),
                'descuento' => $descuento,
                'promo_id'  => $promoId,
            ];
        }

        $resultado = $this->calculadora->calcular($items);
        $totales   = $resultado['totales'];

        $clienteId = (int) ($datos['cliente_id'] ?? 0);
        $clienteId = $clienteId > 0 ? $clienteId : null;

        $pago = $this->pasarela->cobrar((float) $totales['total'], $datos['metodo_pago']);

        $db = db_connect();
        $db->transStart();

        $ventaId = $this->insertarVenta([
            'fecha'       => date('Y-m-d H:i:s'),
            'empleado_id' => (int) $datos['empleado_id'],
            'cliente_id'  => $clienteId,
            'punto_venta' => $datos['punto_venta'],
            'tipo_pago'   => $datos['metodo_pago'],
            'tipo_venta'  => $datos['tipo_venta'],
            'subtotal'    => $totales['subtotal'],
            'descuento'   => $totales['descuento'],
            'total'       => $totales['total'],
        ]);

        foreach ($items as $item) {
            $precioUnitario = round((float) $item['precio'] * (1 - (float) $item['descuento'] / 100), 2);

            for ($i = 0; $i < $item['cantidad']; $i++) {
                $this->boletoModel->insert([
                    'venta_id'     => $ventaId,
                    'tarifa_id'    => $item['tarifa_id'],
                    'promocion_id' => $item['promo_id'] > 0 ? $item['promo_id'] : null,
                    'fecha_visita' => $fechaVisita,
                    'codigo_qr'    => $this->generadorQr->codigo(),
                    'precio'       => $precioUnitario,
                    'estado'       => 'emitido',
                ]);
            }
        }

        $this->pagoModel->insert([
            'venta_id'   => $ventaId,
            'metodo'     => $datos['metodo_pago'],
            'monto'      => $totales['total'],
            'estado'     => $pago['estado'],
            'referencia' => $pago['referencia'],
        ]);

        $db->transComplete();
        if ($db->transStatus() === false) {
            throw new VentaException('No se pudo registrar la venta. Intenta nuevamente.');
        }

        return [
            'venta_id'     => $ventaId,
            'codigo_venta' => $this->ventaModel->find($ventaId)['codigo'],
            'total'        => $totales['total'],
            'lineas'       => $resultado['lineas'],
            'totales'      => $totales,
        ];
    }

    /**
     * Anula una venta completa (motivo obligatorio, sin boletos usados).
     */
    public function anularVenta(int $ventaId, string $motivo, int $anuladoPor): void
    {
        $motivo = trim($motivo);
        if ($motivo === '') {
            throw new VentaException('Debes indicar el motivo de la anulación.');
        }

        $venta = $this->ventaModel->find($ventaId);
        if ($venta === null) {
            throw new VentaException('La venta no existe.');
        }
        if ($venta['estado'] !== 'completada') {
            throw new VentaException('La venta ya no está activa.');
        }

        $boletos = $this->boletoModel->where('venta_id', $ventaId)->findAll();
        foreach ($boletos as $boleto) {
            if ($boleto['estado'] === 'usado') {
                throw new VentaException('No se puede anular: ya hay boletos utilizados en el ingreso.');
            }
        }

        $db = db_connect();
        $db->transStart();

        $this->ventaModel->update($ventaId, [
            'estado'           => 'anulada',
            'motivo_anulacion' => $motivo,
            'fecha_anulacion'  => date('Y-m-d H:i:s'),
            'anulado_por'      => $anuladoPor,
        ]);

        foreach ($boletos as $boleto) {
            $this->boletoModel->update($boleto['id'], ['estado' => 'anulado']);
        }

        $db->transComplete();
        if ($db->transStatus() === false) {
            throw new VentaException('No se pudo anular la venta.');
        }
    }

    private function validarPromoAplicable(int $promoId, int $tarifaId): array
    {
        $promo = $this->promocionModel->find($promoId);
        if ($promo === null || (int) ($promo['activo'] ?? 1) !== 1) {
            throw new VentaException('La promoción seleccionada no está vigente.');
        }

        $hoy = date('Y-m-d');
        if ($promo['fecha_inicio'] > $hoy || $promo['fecha_fin'] < $hoy) {
            throw new VentaException('La promoción seleccionada está fuera de su fecha de vigencia.');
        }

        $aplica = $this->promocionTarifaModel
            ->where('promocion_id', $promoId)
            ->where('tarifa_id', $tarifaId)
            ->first();

        if ($aplica === null) {
            throw new VentaException('La promoción no aplica a la tarifa elegida.');
        }

        return $promo;
    }

    private function insertarVenta(array $datos): int
    {
        $codigoBase = 'V' . date('ymd');
        $codigo     = '';

        for ($i = 0; $i < 5; $i++) {
            $candidato = $codigoBase . str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT);
            $existe    = $this->ventaModel->where('codigo', $candidato)->countAllResults() > 0;
            if (! $existe) {
                $codigo = $candidato;
                break;
            }
        }

        if ($codigo === '') {
            throw new VentaException('No se pudo generar un código único de venta.');
        }

        $this->ventaModel->insert(array_merge($datos, ['codigo' => $codigo, 'estado' => 'completada']));

        if ($this->ventaModel->errors() !== []) {
            throw new VentaException(implode(' ', $this->ventaModel->errors()));
        }

        return (int) $this->ventaModel->getInsertID();
    }
}