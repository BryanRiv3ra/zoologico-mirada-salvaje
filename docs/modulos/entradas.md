# Módulo de Entradas y Promociones

Subsistema de venta de entradas: taquilla (punto de venta), compra en línea
(portal público) y control de acceso con códigos QR.

## Alcance

- **Tarifas** (`entradas.tarifas`): catálogo de precios por tipo de visitante,
  activables/desactivables. Admin de mercadeo y administrador.
- **Promociones** (`entradas.promociones` + `entradas.promocion_tarifa`):
  descuento porcentual válido para fechas, aplicado a tarifas específicas.
  Cada promoción exige al menos una tarifa asociada (D4).
- **Punto de venta** (`entradas.ventas`, `entradas.boletos`, `entradas.pagos`):
  el cajero registra una venta (un cliente opcional -D6-), el sistema emite un
  boleto por entrada con código QR único y registra el pago (D2/D3).
  La venta puede anularse con motivo obligatorio siempre que ningún boleto
  haya sido usado en el ingreso.
- **Portal público** (sin autenticación): el cliente arma su compra en línea,
  la pasarela de pago es simulada, y recibe un comprobante imprimible con QR.
- **Control de acceso**: el personal de puerta valida el QR (se marca `usado`).
- **Reportes**: ingresos por ventas en un rango, con filtros por método de pago
  y cajero; exportación CSV e impresión.

## Roles y rutas

| Ruta | Rol |
|---|---|
| `/portal*` | público |
| `/entradas/taquilla*` | administrador, cajero, supervisor |
| `/entradas/tarifas*` | admin_mercadeo, administrador |
| `/entradas/promociones*` | admin_mercadeo, administrador |
| `/entradas/acceso*` | control_acceso, administrador |
| `/entradas/reportes*` | administrador, supervisor |

## Requisitos de base de datos

Aplicar `database/sql/02_entradas_cambios.sql` (idempotente) sobre el DDL
original:

- `entradas.tarifas.activo` y `entradas.promociones.activo`.
- `entradas.promociones.codigo` UNIQUE.
- Tablas nuevas: `entradas.promocion_tarifa`, `entradas.ventas`,
  `entradas.boletos`.
- `entradas.pagos.venta_id` (FK a la venta; se conserva `entrada_id`).

## Decisiones de diseño

- **D2 (anulación):** `ventas.motivo_anulacion`, `fecha_anulacion` y
  `anulado_por`; se requiere motivo y ningún boleto `usado`.
- **D3 (código):** `ventas.codigo` VARCHAR(12) UNIQUE, generado por el sistema.
- **D6 (taquilla anónima):** `empleado_id` y `cliente_id` son opcionales; el
  portal siempre registra el visitante.
- **D7 (QR/PDF sin Composer):** `boletos.codigo_qr` contiene el token único
  (`MS-…`); la imagen QR se obtiene del servicio público api.qrserver.com y el
  comprobante es una vista imprimible con `window.print()`.

## Integración con autenticación (core)

Igual que Limpieza: los filtros `auth`/`rol` (`app/Filters`) son provisionales y
deben ser reemplazados por la autenticación real (módulo core-autenticación).
`app/Controllers/Dev.php` permite simular las sesiones de `cajero`,
`admin_mercadeo` y `control_acceso` SOLO en `CI_ENVIRONMENT=development`.

## Notas técnicas

- El motor de ventas vive en `app/Libraries/Entradas/ServicioVentas.php`
  (transacciones, validación de promociones, emisión de boletos y pagos).
- `CalculadoraVenta` centraliza subtotal/descuento/total; `PasarelaPagoSimulada`
  es el punto único de integración con una pasarela real.
- Los montos se manejan en quetzales (Q), NUMERIC(10,2).
- PHP 8.2+ · PostgreSQL (controlador `Postgre`).