<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Animales del zoológico (tabla core.animales).
 *
 * Estructura real (verificada en information_schema):
 *   id                 serial      PK
 *   nombre             varchar(100) NOT NULL
 *   especie_id         int         NOT NULL  FK core.especies(id)
 *   zona_id            int         NULL      FK core.zonas(id)
 *   fecha_nacimiento   date        NULL
 *   sexo               char(1)     NULL      CHECK IN ('M','F','N')
 *   estado             varchar(20) NOT NULL  DEFAULT 'activo'
 *                                       CHECK IN ('activo','en_tratamiento',
 *                                                 'fallecido','trasladado')
 *   creado_en          timestamp   NOT NULL  DEFAULT now()
 *
 * No hay columna para el motivo de la baja, así que la baja lógica solo
 * cambia `estado`. El borrado físico está descartado: clinico.historial_clinico,
 * clinico.aplicaciones_vacunas y alimentacion.dietas tienen FK hacia
 * core.animales con ON DELETE CASCADE, y borrar un animal destruiría su historia.
 */
class AnimalModel extends Model
{
    protected $table            = 'core.animales';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = ['nombre', 'especie_id', 'zona_id', 'fecha_nacimiento', 'sexo', 'estado'];

    /**
     * Valores admitidos por el CHECK animales_sexo_check (bpchar(1)).
     *
     * @var array<string, string>
     */
    public const SEXOS = [
        'M' => 'Macho',
        'F' => 'Hembra',
        'N' => 'No especificado',
    ];

    /**
     * Valores admitidos por el CHECK animales_estado_check.
     *
     * @var array<string, string>
     */
    public const ESTADOS = [
        'activo'          => 'Activo',
        'en_tratamiento'  => 'En tratamiento',
        'fallecido'       => 'Fallecido',
        'trasladado'      => 'Trasladado',
    ];

    /**
     * Estados en los que el animal deja de estar operativo y se considera
     * dado de baja. 'activo' y 'en_tratamiento' NO lo son: un animal en
     * tratamiento sigue visible en los <select> de Clínico y Dietas.
     *
     * @var list<string>
     */
    public const ESTADOS_BAJA = ['fallecido', 'trasladado'];

    /**
     * Lista de animales activos, para llenar el <select> del formulario.
     *
     * NO cambiar la firma ni el criterio: la usan Clinico, ClinicoVacunas y
     * Alimentacion/Dietas.
     */
    public function listaActivos(): array
    {
        return $this->where('estado', 'activo')
                    ->orderBy('nombre', 'ASC')
                    ->findAll();
    }

    /**
     * Listado del CRUD con el nombre de la especie y de la zona resueltos,
     * más los filtros de la pantalla de búsqueda.
     *
     * @param array{
     *     especie_id?: string|null,
     *     zona_id?: string|null,
     *     estado?: string|null,
     *     busqueda?: string|null
     * } $filtros
     *
     * @return list<array<string, mixed>>
     */
    public function listar(array $filtros = []): array
    {
        $builder = $this->builder()
            ->select('core.animales.*, e.nombre_comun AS especie, z.nombre AS zona')
            ->join('core.especies AS e', 'e.id = core.animales.especie_id', 'left')
            ->join('core.zonas AS z', 'z.id = core.animales.zona_id', 'left');

        if (! empty($filtros['especie_id'])) {
            $builder->where('core.animales.especie_id', (int) $filtros['especie_id']);
        }

        if (! empty($filtros['zona_id'])) {
            $builder->where('core.animales.zona_id', (int) $filtros['zona_id']);
        }

        if (! empty($filtros['estado'])) {
            $builder->where('core.animales.estado', $filtros['estado']);
        }

        if (! empty($filtros['busqueda'])) {
            // PostgreSQL: LIKE SÍ distingue mayúsculas (a diferencia de MySQL) y
            // CI4 no reconoce ILIKE como operador (lo entrecomilla y rompe el
            // SQL), así que se baja el lado izquierdo con LOWER().
            $builder->where('LOWER(core.animales.nombre) LIKE', '%' . $this->escaparLike(mb_strtolower($filtros['busqueda'])) . '%');
        }

        return $builder->orderBy('core.animales.nombre', 'asc')->get()->getResultArray();
    }

    /**
     * Un animal con su especie y su zona, para el formulario de edición.
     *
     * @return array<string, mixed>|null
     */
    public function findConRelaciones(int $id): ?array
    {
        return $this->builder()
            ->select('core.animales.*, e.nombre_comun AS especie, z.nombre AS zona')
            ->join('core.especies AS e', 'e.id = core.animales.especie_id', 'left')
            ->join('core.zonas AS z', 'z.id = core.animales.zona_id', 'left')
            ->where('core.animales.id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Escapa los comodines de LIKE/ILIKE para que se busquen literales.
     *
     * Sin esto, buscar "%" traería todos los animales y buscar "_" haría
     * coincidir cualquier carácter. El valor sigue yendo con bind parameter
     * (CI4 lo escapa como parámetro), así que esto solo afecta al sentido de
     * la búsqueda, nunca a la seguridad.
     */
    private function escaparLike(string $texto): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $texto);
    }

    /**
     * Comprueba que la zona exista y esté activa.
     *
     * Las zonas tienen `activo` BOOLEAN, y PostgreSQL lo devuelve como 't'/'f',
     * de ahí el helper es_activo().
     */
    public function zonaActivaExiste(int $zonaId): bool
    {
        helper('zoologico');

        $zona = $this->db->table('core.zonas')->where('id', $zonaId)->get()->getRowArray();

        return $zona !== null && es_activo($zona['activo'] ?? null);
    }

    /**
     * Edad legible a partir de la fecha de nacimiento.
     *
     * Se calcula en PHP y no en SQL para que el texto sea el mismo en todos los
     * navegadores. Devuelve null si no hay fecha o si la fecha es inválida.
     */
    public static function edadLegible(?string $fechaNacimiento): ?string
    {
        if ($fechaNacimiento === null || $fechaNacimiento === '') {
            return null;
        }

        try {
            $nacimiento = new \DateTimeImmutable($fechaNacimiento);
        } catch (\Exception) {
            return null;
        }

        $hoy = new \DateTimeImmutable('today');
        $dif = $hoy->diff($nacimiento);

        // Un fecha futura (no debería pasar: el formulario lo valida) daría un
        // intervalo negativo; se trata como edad desconocida.
        if ($nacimiento > $hoy) {
            return null;
        }

        if ($dif->y > 0) {
            $anos = $dif->y;
            $mes  = $dif->m;

            return $anos === 1
                ? '1 año' . ($mes > 0 ? ' y ' . $mes . ' ' . ($mes === 1 ? 'mes' : 'meses') : '')
                : $anos . ' años' . ($mes > 0 ? ' y ' . $mes . ' ' . ($mes === 1 ? 'mes' : 'meses') : '');
        }

        if ($dif->m > 0) {
            return $dif->m . ' ' . ($dif->m === 1 ? 'mes' : 'meses');
        }

        if ($dif->d > 0) {
            return $dif->d . ' ' . ($dif->d === 1 ? 'día' : 'días');
        }

        return 'recién nacido';
    }

    /**
     * Clase CSS del status-chip según el estado.
     *
     * Reutiliza las variantes que ya define public/assets/css/limpieza.css.
     */
    public static function claseEstado(string $estado): string
    {
        return match ($estado) {
            'activo'         => 'is-activa',
            'en_tratamiento' => 'is-en_curso',
            'fallecido'      => 'is-pendiente',
            default          => 'is-inactivo',
        };
    }
}
