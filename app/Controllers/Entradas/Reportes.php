<?php

namespace App\Controllers\Entradas;

use App\Controllers\BaseController;
use App\Models\Core\EmpleadoModel;
use App\Models\Entradas\VentaModel;

/**
 * Reportes de ventas. Permiso: administrador, supervisor.
 */
class Reportes extends BaseController
{
    protected $helpers = ['url', 'form'];

    public function index(): string
    {
        $filtros = [
            'desde'      => $this->request->getGet('desde') ?? date('Y-m-01'),
            'hasta'      => $this->request->getGet('hasta') ?? date('Y-m-d'),
            'tipo_pago'  => $this->request->getGet('tipo_pago') ?? '',
            'empleado'   => (int) ($this->request->getGet('empleado') ?? 0),
        ];

        $ventas = model(VentaModel::class)->reporte(
            $filtros['desde'],
            $filtros['hasta'],
            $filtros['tipo_pago'],
            $filtros['empleado']
        );

        $totales = $this->calcularTotales($ventas);

        return view('entradas/reportes/index', [
            'titulo'    => 'Reporte de ventas',
            'cssExtra'  => 'entradas.css',
            'filtros'   => $filtros,
            'ventas'    => $ventas,
            'totales'   => $totales,
            'metodos'   => ['', 'efectivo', 'tarjeta', 'transferencia'],
            'empleados' => model(EmpleadoModel::class)->activosConRol('cajero'),
        ]);
    }

    public function imprimir(): string
    {
        $filtros = [
            'desde'     => $this->request->getGet('desde') ?? date('Y-m-01'),
            'hasta'     => $this->request->getGet('hasta') ?? date('Y-m-d'),
            'tipo_pago' => $this->request->getGet('tipo_pago') ?? '',
            'empleado'  => (int) ($this->request->getGet('empleado') ?? 0),
        ];

        $ventas = model(VentaModel::class)->reporte(
            $filtros['desde'],
            $filtros['hasta'],
            $filtros['tipo_pago'],
            $filtros['empleado']
        );

        return view('entradas/reportes/imprimir', [
            'filtros'   => $filtros,
            'ventas'    => $ventas,
            'totales'   => $this->calcularTotales($ventas),
            'generado'  => date('Y-m-d H:i'),
        ]);
    }

    public function exportarCsv()
    {
        $filtros = [
            'desde'     => $this->request->getGet('desde') ?? date('Y-m-01'),
            'hasta'     => $this->request->getGet('hasta') ?? date('Y-m-d'),
            'tipo_pago' => $this->request->getGet('tipo_pago') ?? '',
            'empleado'  => (int) ($this->request->getGet('empleado') ?? 0),
        ];

        $ventas = model(VentaModel::class)->reporte(
            $filtros['desde'],
            $filtros['hasta'],
            $filtros['tipo_pago'],
            $filtros['empleado']
        );

        $respuesta = $this->response;
        $respuesta->setHeader('Content-Type', 'text/csv; charset=utf-8');
        $respuesta->setHeader('Content-Disposition', 'attachment; filename="reporte_ventas_' . date('Ymd_His') . '.csv"');
        $data = "Codigo,Fecha,Empleado,TipoVenta,TipoPago,Subtotal,Descuento,Total,Estado\n";

        foreach ($ventas as $v) {
            $v = array_map(fn ($valor): string => str_replace('"', '""', (string) $valor), $v);
            $nombreEmpleado = trim(($v['empleado_nombre'] ?? '') . ' ' . ($v['empleado_apellido'] ?? ''));
            $data .= implode(',', [
                '"' . $v['codigo'] . '"',
                '"' . $v['fecha'] . '"',
                '"' . $nombreEmpleado . '"',
                $v['tipo_venta'],
                $v['tipo_pago'],
                $v['subtotal'],
                $v['descuento'],
                $v['total'],
                $v['estado'],
            ]) . "\n";
        }

        $respuesta->setBody($data);

        return $respuesta;
    }

    private function calcularTotales(array $ventas): array
    {
        $completadas = array_filter($ventas, fn ($v) => $v['estado'] === 'completada');
        $detalle = model(VentaModel::class);

        $boletos = 0;
        foreach ($completadas as $v) {
            $boletos += $detalle->contarBoletos((int) $v['id']);
        }

        return [
            'ventas'     => count($ventas),
            'completadas' => count($completadas),
            'anuladas'   => count($ventas) - count($completadas),
            'ingresos'   => array_sum(array_column($completadas, 'total')),
            'descuentos' => array_sum(array_column($ventas, 'descuento')),
            'boletos'    => $boletos,
            'porMetodo'  => $this->totalesPorMetodo($completadas),
        ];
    }

    private function totalesPorMetodo(array $ventas): array
    {
        $metodos = [];
        foreach ($ventas as $v) {
            $metodo = $v['tipo_pago'];
            $metodos[$metodo] = ($metodos[$metodo] ?? 0) + (float) $v['total'];
        }

        return $metodos;
    }
}