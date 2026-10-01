<?php

namespace App\Controllers\Limpieza;

use App\Controllers\BaseController;
use App\Models\Core\ZonaModel;

/**
 * Gestión de zonas de limpieza (CU: Gestionar zonas).
 * Permiso: administrador.
 */
class Zonas extends BaseController
{
    protected ZonaModel $zonas;

    public function __construct()
    {
        $this->zonas = model(ZonaModel::class);
    }

    public function index(): string
    {
        $tipoFiltro = $this->request->getGet('tipo') ?? '';

        $query = $this->zonas->orderBy('nombre', 'asc');
        if ($tipoFiltro !== '') {
            $query->where('tipo', $tipoFiltro);
        }

        return view('limpieza/zonas/index', [
            'titulo'     => 'Zonas de Limpieza',
            'zonas'      => $query->findAll(),
            'tipos'      => $this->tiposZona(),
            'tipoFiltro' => $tipoFiltro,
        ]);
    }

    public function nueva(): string
    {
        return view('limpieza/zonas/form', [
            'titulo' => 'Nueva Zona',
            'modo'   => 'crear',
            'zona'   => ['id' => null, 'nombre' => '', 'tipo' => 'jaula', 'ubicacion' => '', 'capacidad' => '', 'activo' => true],
            'tipos'  => $this->tiposZona(),
        ]);
    }

    public function editar(int $id)
    {
        $zona = $this->zonas->find($id);
        if ($zona === null) {
            return $this->noExiste('La zona no existe.');
        }

        return view('limpieza/zonas/form', [
            'titulo' => 'Editar Zona',
            'modo'   => 'editar',
            'zona'   => $zona,
            'tipos'  => $this->tiposZona(),
        ]);
    }

    public function guardar()
    {
        $datos = $this->validarDatos();
        if ($datos === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($this->zonas->existeNombre($datos['nombre'])) {
            return redirect()->back()->withInput()->with('error', 'Ya existe una zona con ese nombre.');
        }

        $this->zonas->insert($datos);

        return redirect()->to('limpieza/zonas')->with('success', 'Zona creada correctamente.');
    }

    public function actualizar(int $id)
    {
        if ($this->zonas->find($id) === null) {
            return $this->noExiste('La zona no existe.');
        }

        $datos = $this->validarDatos();
        if ($datos === null) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($this->zonas->existeNombre($datos['nombre'], $id)) {
            return redirect()->back()->withInput()->with('error', 'Ya existe otra zona con ese nombre.');
        }

        $this->zonas->update($id, $datos);

        return redirect()->to('limpieza/zonas')->with('success', 'Zona actualizada correctamente.');
    }

    /**
     * Baja lógica: activo = false. Nunca DELETE (hay historial y la usan otros módulos).
     */
    public function desactivar(int $id)
    {
        if ($this->zonas->find($id) === null) {
            return $this->noExiste('La zona no existe.');
        }

        $this->zonas->update($id, ['activo' => false]);

        return redirect()->to('limpieza/zonas')->with('success', 'Zona desactivada.');
    }

    protected function validarDatos(): ?array
    {
        $reglas = [
            'nombre'    => 'required|max_length[100]',
            'tipo'      => 'required|in_list[jaula,sanitario,jardin,area_juegos,oficina]',
            'ubicacion' => 'permit_empty|max_length[150]',
            'capacidad' => 'permit_empty|is_natural',
        ];

        if (! $this->validate($reglas)) {
            return null;
        }

        return [
            'nombre'    => trim($this->request->getPost('nombre')),
            'tipo'      => $this->request->getPost('tipo'),
            'ubicacion' => trim((string) $this->request->getPost('ubicacion')),
            'capacidad' => $this->request->getPost('capacidad') === '' ? null : (int) $this->request->getPost('capacidad'),
        ];
    }

    /**
     * @return string[]
     */
    protected function tiposZona(): array
    {
        return [
            'jaula'       => 'Recinto / Jaula',
            'sanitario'   => 'Servicio Sanitario',
            'jardin'      => 'Sendero / Jardín',
            'area_juegos' => 'Área Recreativa',
            'oficina'     => 'Oficina Administrativa',
        ];
    }

    protected function noExiste(string $mensaje)
    {
        return redirect()->to('limpieza/zonas')->with('error', $mensaje);
    }
}