<?php

namespace App\Controllers\Limpieza;

use App\Controllers\BaseController;
use App\Models\Core\EmpleadoModel;
use App\Models\Core\ZonaModel;

/**
 * Reporte de limpieza (CU: Generar reporte de limpieza).
 * Cumplimiento global y por zona/empleado, pendientes vencidas, duración promedio.
 * Permiso: administrador, supervisor.
 */
class Reportes extends BaseController
{
    protected ZonaModel $zonas;
    protected EmpleadoModel $empleados;

    public function __construct()
    {
        $this->zonas     = model(ZonaModel::class);
        $this->empleados = model(EmpleadoModel::class);
    }

    public function index(): string
    {
        $filtros = $this->filtros();

        $db        = db_connect();
        $asignaciones = $this->consultaAsignaciones($db, $filtros);

        $totales = [
            'asignadas'   => count($asignaciones),
            'listas'      => count(array_filter($asignaciones, fn ($a) => $a['estado'] === 'listo')),
            'en_curso'    => count(array_filter($asignaciones, fn ($a) => $a['estado'] === 'en_curso')),
            'pendientes'  => count(array_filter($asignaciones, fn ($a) => $a['estado'] === 'pendiente')),
            'vencidas'    => count(array_filter($asignaciones, fn ($a) => $a['estado'] !== 'listo' && $a['fecha_programada'] < date('Y-m-d'))),
        ];
        $totales['cumplimiento'] = $totales['asignadas'] > 0
            ? round($totales['listas'] / $totales['asignadas'] * 100, 1)
            : 0.0;

        return view('limpieza/reportes/index', [
            'titulo'         => 'Reporte de Limpieza',
            'filtros'        => $filtros,
            'zonas'          => $this->zonas->listaActivas(),
            'empleados'      => $this->empleados->activosConRol('empleado_limpieza'),
            'totales'        => $totales,
            'porZona'        => $this->resumen($asignaciones, 'zona_nombre'),
            'porEmpleado'    => $this->resumen($asignaciones, 'empleado_nombre', 'empleado_apellido'),
            'registros'      => $asignaciones,
            'duracionPromedio' => $this->duracionPromedio($asignaciones),
        ]);
    }

    /**
     * Exporta el reporte a CSV sin dependencias.
     */
    public function exportarCsv()
    {
        $filas = $this->filasCsv();

        $salida = fopen('php://temp', 'w');
        fputcsv($salida, ['Zona', 'Tipo', 'Empleado', 'Fecha_programada', 'Estado', 'Inicio_real', 'Fin_real', 'Observaciones']);
        foreach ($filas as $fila) {
            fputcsv($salida, $fila);
        }
        rewind($salida);
        $contenido = stream_get_contents($salida);
        fclose($salida);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="reporte_limpieza_' . date('Ymd_His') . '.csv"')
            ->setBody("\xEF\xBB\xBF" . $contenido);
    }

    protected function filasCsv(): array
    {
        $db           = db_connect();
        $asignaciones = $this->consultaAsignaciones($db, $this->filtros());

        return array_map(static fn ($a): array => [
            $a['zona_nombre'] ?? '',
            $a['zona_tipo'] ?? '',
            trim(($a['empleado_nombre'] ?? '') . ' ' . ($a['empleado_apellido'] ?? '')),
            $a['fecha_programada'],
            $a['estado'],
            $a['inicio_real'] ?? '',
            $a['fin_real'] ?? '',
            $a['observaciones'] ?? '',
        ], $asignaciones);
    }

    /**
     * Vista imprimible del reporte.
     */
    public function imprimir(): string
    {
        $db           = db_connect();
        $asignaciones = $this->consultaAsignaciones($db, $this->filtros());

        return view('limpieza/reportes/imprimir', [
            'titulo'    => 'Reporte de Limpieza (Impresión)',
            'filtros'   => $this->filtros(),
            'registros' => $asignaciones,
            'generado'  => date('Y-m-d H:i:s'),
        ]);
    }

    protected function consultaAsignaciones($db, array $filtros): array
    {
        $query = $db->table('limpieza.asignaciones_limpieza as a')
            ->select("a.*, t.descripcion, t.frecuencia, z.nombre as zona_nombre, z.tipo as zona_tipo,
                      e.nombre as empleado_nombre, e.apellido as empleado_apellido,
                      r.observaciones")
            ->join('limpieza.tareas_limpieza as t', 't.id = a.tarea_id')
            ->join('core.zonas as z', 'z.id = t.zona_id')
            ->join('core.empleados as e', 'e.id = a.empleado_id')
            ->join('limpieza.registros_limpieza as r', 'r.asignacion_id = a.id', 'left')
            ->orderBy('a.fecha_programada', 'asc')
            ->orderBy('z.nombre', 'asc');

        if ($filtros['desde'] !== '') {
            $query->where('a.fecha_programada >=', $filtros['desde']);
        }
        if ($filtros['hasta'] !== '') {
            $query->where('a.fecha_programada <=', $filtros['hasta']);
        }
        if ($filtros['zona'] > 0) {
            $query->where('t.zona_id', $filtros['zona']);
        }
        if ($filtros['tipo'] !== '') {
            $query->where('z.tipo', $filtros['tipo']);
        }
        if ($filtros['empleado'] > 0) {
            $query->where('a.empleado_id', $filtros['empleado']);
        }

        return $query->get()->getResultArray();
    }

    /**
     * Agrupa asignaciones por una clave (zona o empleado) y resume cumplimiento.
     */
    protected function resumen(array $asignaciones, string $clave, string $apellidoClave = ''): array
    {
        $grupos = [];
        foreach ($asignaciones as $a) {
            $nombre = $a[$clave] ?? 'Sin asignar';
            if ($apellidoClave !== '' && ! empty($a[$apellidoClave])) {
                $nombre .= ' ' . $a[$apellidoClave];
            }
            $grupos[$nombre][] = $a;
        }

        $resumen = [];
        foreach ($grupos as $nombre => $items) {
            $listas = count(array_filter($items, fn ($a) => $a['estado'] === 'listo'));
            $resumen[] = [
                'nombre'       => $nombre,
                'asignadas'    => count($items),
                'listas'       => $listas,
                'cumplimiento' => round($listas / count($items) * 100, 1),
            ];
        }

        usort($resumen, static fn ($x, $y) => $y['asignadas'] <=> $x['asignadas']);

        return $resumen;
    }

    /**
     * Duración promedio (fin_real - inicio_real) de las asignaciones listas.
     */
    protected function duracionPromedio(array $asignaciones): string
    {
        $duraciones = [];
        foreach ($asignaciones as $a) {
            if ($a['estado'] === 'listo' && ! empty($a['inicio_real']) && ! empty($a['fin_real'])) {
                $inicio = strtotime($a['inicio_real']);
                $fin    = strtotime($a['fin_real']);
                if ($fin >= $inicio) {
                    $duraciones[] = $fin - $inicio;
                }
            }
        }

        if ($duraciones === []) {
            return '—';
        }

        $promedio = (int) round(array_sum($duraciones) / count($duraciones));

        return sprintf('%02d h %02d min', intdiv($promedio, 3600), intdiv($promedio % 3600, 60));
    }

    protected function filtros(): array
    {
        return [
            'desde'    => $this->request->getGet('desde') ?? '',
            'hasta'    => $this->request->getGet('hasta') ?? '',
            'zona'     => (int) ($this->request->getGet('zona') ?? 0),
            'tipo'     => $this->request->getGet('tipo') ?? '',
            'empleado' => (int) ($this->request->getGet('empleado') ?? 0),
        ];
    }
}