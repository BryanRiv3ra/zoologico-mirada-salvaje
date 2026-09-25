<?php

namespace App\Libraries\Entradas;

/**
 * Generación del contenido de los códigos QR de boletos y de su imagen.
 *
 * Sin dependencias de Composer (D7): el boleto guarda un token único en
 * `boletos.codigo_qr` y la imagen se produce con el servicio público
 * api.qrserver.com. En producción se puede sustituir el renderizador por una
 * librería local conservando los mismos métodos.
 */
class GeneradorQr
{
    public function codigo(): string
    {
        return 'MS-' . strtoupper(bin2hex(random_bytes(7)));
    }

    public function imagenUrl(string $codigo, int $size = 180): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size='
            . $size . 'x' . $size
            . '&data=' . rawurlencode($codigo);
    }
}