<?php

namespace App\Libraries\Entradas;

use App\Models\Entradas\PagoModel;
use App\Models\Entradas\PromocionModel;
use App\Models\Entradas\TarifaModel;

/**
 * Registra boletos individuales, adaptado al modelo E-R aprobado
 * (entradas.entradas + entradas.pagos). No agrupa boletos en una "venta":
 * cada boleto comprado es su propia fila, con su propio pago.
 */
class ServicioVentas
{
    private TarifaModel $tarifaModel;
    private PromocionModel $promocionModel;
    private PagoModel $pagoModel;
    private GeneradorQr $generadorQr;
    private PasarelaPagoSimulada $pasarela;

    public function __construct()
    {
        $this->tarifaModel    = new TarifaModel();
        $this->promocionModel = new PromocionModel();
        $this->pagoModel      = new PagoModel();
        $this->generadorQr    = new GeneradorQr();
        $this->pasarela       = new PasarelaPagoSimulada();
    }

    /**
     * @param list<array{tarifa_id:int, cantidad:int, promo_id:int|null}> $lineas
     * @return array{entrada_ids:list<int>, primer_id:int, total:float, cantidad:int}
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

        $visitanteId = (int) ($datos['cliente_id'] ?? 0);
        if ($visitanteId <= 0) {
            throw new VentaException('No se pudo determinar el visitante para esta compra.');
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
                $promo = $this->validarPromoAplicable($promoId);
                $descuento = (float) $promo['descuento'];
            }

            $items[] = [
                'tarifa_id' => $tarifaId,
                'precio'    => (float) $mapaTarifas[$tarifaId]['precio'],
                'cantidad'  => max(1, (int) ($linea['cantidad'] ?? 1)),
                'descuento' => $descuento,
                'promo_id'  => $promoId,
            ];
        }

        $db = db_connect();
        $db->transStart();

        $entradaIds   = [];
        $totalGeneral = 0.0;

        foreach ($items as $item) {
            $precioUnitario = round($item['precio'] * (1 - $item['descuento'] / 100), 2);

            for ($i = 0; $i < $item['cantidad']; $i++) {
                $pago = $this->pasarela->cobrar($precioUnitario, $datos['metodo_pago']);

                $db->table('entradas.entradas')->insert([
                    'visitante_id' => $visitanteId,
                    'tarifa_id'    => $item['tarifa_id'],
                    'promocion_id' => $item['promo_id'] > 0 ? $item['promo_id'] : null,
                    'fecha_compra' => date('Y-m-d H:i:s'),
                    'fecha_visita' => $fechaVisita,
                    'codigo_qr'    => $this->generadorQr->codigo(),
                    'total'        => $precioUnitario,
                ]);

                $entradaId      = (int) $db->insertID();
                $entradaIds[]   = $entradaId;
                $totalGeneral  += $precioUnitario;

                $this->pagoModel->insert([
                    'entrada_id' => $entradaId,
                    'metodo'     => $datos['metodo_pago'],
                    'monto'      => $precioUnitario,
                    'fecha'      => date('Y-m-d H:i:s'),
                    'estado'     => $pago['estado'],
                    'referencia' => $pago['referencia'],
                ]);
            }
        }

        $db->transComplete();
        if ($db->transStatus() === false) {
            throw new VentaException('No se pudo registrar la venta. Intenta nuevamente.');
        }

        return [
            'entrada_ids' => $entradaIds,
            'primer_id'   => $entradaIds[0],
            'total'       => $totalGeneral,
            'cantidad'    => count($entradaIds),
        ];
    }

    /**
     * "Anular" aquí marca el pago del boleto como rechazado (único estado
     * disponible para esto en el CHECK de entradas.pagos). No existe un
     * estado de boleto "usado/anulado" en el E-R actual.
     */
    public function anularVenta(int $entradaId, string $motivo, int $anuladoPor): void
    {
        $motivo = trim($motivo);
        if ($motivo === '') {
            throw new VentaException('Debes indicar el motivo de la anulación.');
        }

        $db = db_connect();
        $pago = $db->table('entradas.pagos')
            ->where('entrada_id', $entradaId)
            ->orderBy('id', 'desc')
            ->get()
            ->getRowArray();

        if ($pago === null) {
            throw new VentaException('No se encontró el pago de este boleto.');
        }
        if ($pago['estado'] === 'rechazado') {
            throw new VentaException('Este boleto ya está anulado.');
        }

        $db->table('entradas.pagos')->where('id', $pago['id'])->update([
            'estado'     => 'rechazado',
            'referencia' => trim(($pago['referencia'] ?? '') . ' | Anulado: ' . $motivo),
        ]);
    }

    private function validarPromoAplicable(int $promoId): array
    {
        $promo = $this->promocionModel->find($promoId);
        if ($promo === null) {
            throw new VentaException('La promoción seleccionada no existe.');
        }

        $hoy = date('Y-m-d');
        if ($promo['fecha_inicio'] > $hoy || $promo['fecha_fin'] < $hoy) {
            throw new VentaException('La promoción seleccionada está fuera de su fecha de vigencia.');
        }

        return $promo;
    }
}