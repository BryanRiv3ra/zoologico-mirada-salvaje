<?php

if (! function_exists('es_activo')) {
    /**
     * Normaliza el valor de una columna `activo` a booleano.
     *
     * El driver de PostgreSQL devuelve los booleanos como cadenas 't' / 'f',
     * y las columnas que se crearon como SMALLINT llegan como 1 / 0. Un
     * `(int) $x['activo'] === 1` da false para 't', y un `if ($x['activo'])`
     * da true para 'f' (cadena no vacia). Esta funcion cubre ambos casos.
     *
     * Usar en vistas y en consultas hechas con Query Builder directo
     * ($db->table(...)), que no pasan por los $casts del modelo.
     */
    function es_activo(mixed $valor): bool
    {
        if (is_bool($valor)) {
            return $valor;
        }

        if (is_int($valor) || is_float($valor)) {
            return (int) $valor === 1;
        }

        if (is_string($valor)) {
            return in_array(strtolower(trim($valor)), ['t', 'true', '1'], true);
        }

        return $valor !== null && $valor !== 0 && $valor !== '';
    }
}
