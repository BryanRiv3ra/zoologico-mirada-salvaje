<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Agrega la columna `activo` (BOOLEAN) a las tres tablas del catálogo.
 *
 * NOTA: si `core.zonas.activo` ya existe como SMALLINT (aplicado antes por
 * script manual o por la version anterior de esta migracion), IF NOT EXISTS la
 * dejaria intacta y `where('activo', true)` seguiria fallando con
 * "operator does not exist: smallint = boolean". Por eso se convierte el tipo
 * explicitamente. Vease database/sql/01_limpieza_cambios.sql.
 */
class AgregarColumnaActivo extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE core.zonas ADD COLUMN IF NOT EXISTS activo BOOLEAN NOT NULL DEFAULT TRUE');
        $this->db->query('ALTER TABLE entradas.tarifas ADD COLUMN IF NOT EXISTS activo BOOLEAN NOT NULL DEFAULT TRUE');
        $this->db->query('ALTER TABLE entradas.promociones ADD COLUMN IF NOT EXISTS activo BOOLEAN NOT NULL DEFAULT TRUE');

        // Normaliza core.zonas.activo a BOOLEAN si venia como SMALLINT.
        $tipoZona = $this->db->query(
            "SELECT data_type FROM information_schema.columns
             WHERE table_schema = 'core' AND table_name = 'zonas' AND column_name = 'activo'"
        )->getRowArray();

        if (($tipoZona['data_type'] ?? null) === 'smallint') {
            $this->db->query('ALTER TABLE core.zonas ALTER COLUMN activo DROP DEFAULT');
            $this->db->query('ALTER TABLE core.zonas ALTER COLUMN activo TYPE BOOLEAN USING activo <> 0');
            $this->db->query('ALTER TABLE core.zonas ALTER COLUMN activo SET DEFAULT TRUE');
        }
    }

    public function down()
    {
        $this->db->query('ALTER TABLE core.zonas DROP COLUMN IF EXISTS activo');
        $this->db->query('ALTER TABLE entradas.tarifas DROP COLUMN IF EXISTS activo');
        $this->db->query('ALTER TABLE entradas.promociones DROP COLUMN IF EXISTS activo');
    }
}