<?php

namespace App\Libraries\Limpieza;

/**
 * Máquina de estados de una asignación de limpieza.
 * Refleja «enum EstadoAsignacion {PENDIENTE, EN_CURSO, LISTO}» del diagrama de clases.
 */
enum EstadoAsignacion: string
{
    case PENDIENTE = 'pendiente';
    case EN_CURSO  = 'en_curso';
    case LISTO     = 'listo';

    /**
     * La única transición válida es pendiente → en_curso → listo.
     * No se permiten saltos ni retrocesos.
     */
    public static function transicionValida(string $actual, string $destino): bool
    {
        return ($actual === self::PENDIENTE->value && $destino === self::EN_CURSO->value)
            || ($actual === self::EN_CURSO->value && $destino === self::LISTO->value);
    }
}