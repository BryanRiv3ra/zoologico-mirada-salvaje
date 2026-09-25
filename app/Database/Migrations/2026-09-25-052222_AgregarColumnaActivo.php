<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AgregarColumnaActivo extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE core.zonas ADD COLUMN IF NOT EXISTS activo SMALLINT NOT NULL DEFAULT 1');

        $this->db->query('ALTER TABLE entradas.tarifas ADD COLUMN IF NOT EXISTS activo BOOLEAN NOT NULL DEFAULT TRUE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE core.zonas DROP COLUMN IF EXISTS activo');
        $this->db->query('ALTER TABLE entradas.tarifas DROP COLUMN IF EXISTS activo');
    }

    public function up()
    {
        $this->db->query('ALTER TABLE core.zonas ADD COLUMN IF NOT EXISTS activo SMALLINT NOT NULL DEFAULT 1');
        $this->db->query('ALTER TABLE entradas.tarifas ADD COLUMN IF NOT EXISTS activo BOOLEAN NOT NULL DEFAULT TRUE');
        $this->db->query('ALTER TABLE entradas.promociones ADD COLUMN IF NOT EXISTS activo BOOLEAN NOT NULL DEFAULT TRUE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE core.zonas DROP COLUMN IF EXISTS activo');
        $this->db->query('ALTER TABLE entradas.tarifas DROP COLUMN IF EXISTS activo');
        $this->db->query('ALTER TABLE entradas.promociones DROP COLUMN IF EXISTS activo');
    }
}