<?php

namespace App\Controllers\Entradas;

use App\Controllers\BaseController;
use App\Models\Entradas\PromocionModel;

/**
 * Gestión de promociones. Permiso: admin_mercadeo, administrador.
 * Adaptado al ER real: toda promoción vigente aplica automáticamente
 * a todas las tarifas activas (no existe relación promoción-tarifa).
 */
class Promociones extends BaseController
{
    protected $helpers = ['url', 'form'];

    private PromocionModel $promociones;

    public function __construct()
    {
        $this->promociones = model(PromocionModel::class);
    }

    public function index(): string
    {
        return view('entradas/promociones/index', [
            'titulo'   => 'Promociones',
            'cssExtra' => 'entradas.css',
            'promos'   => $this->promociones->todas(),
        ]);
    }

    public function nueva(): string
    {
        return view('entradas/promociones/form', [
            'titulo'   => 'Nueva promoción',
            'cssExtra' => 'entradas.css',
            'promo'    => null,
        ]);
    }

    public function guardar()
    {
        $datos = $this->postDatos();

        if ($datos['fecha_fin'] < $datos['fecha_inicio']) {
            return redirect()->back()->withInput()->with('error', 'La fecha fin no puede ser anterior a la fecha inicio.');
        }

        if (! $this->promociones->validate($datos)) {
            return redirect()->back()->withInput()->with('errors', $this->promociones->errors());
        }

        $this->promociones->insert($datos);

        return redirect()->to('/entradas/promociones')->with('success', 'Promoción creada correctamente.');
    }

    public function editar(int $id): string
    {
        $promo = $this->promociones->find($id);
        if ($promo === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('entradas/promociones/form', [
            'titulo'   => 'Editar promoción',
            'cssExtra' => 'entradas.css',
            'promo'    => $promo,
        ]);
    }

    public function actualizar(int $id)
    {
        $promo = $this->promociones->find($id);
        if ($promo === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $datos = $this->postDatos();

        if ($datos['fecha_fin'] < $datos['fecha_inicio']) {
            return redirect()->back()->withInput()->with('error', 'La fecha fin no puede ser anterior a la fecha inicio.');
        }

        if (! $this->promociones->validate($datos)) {
            return redirect()->back()->withInput()->with('errors', $this->promociones->errors());
        }

        $this->promociones->update($id, $datos);

        return redirect()->to('/entradas/promociones')->with('success', 'Promoción actualizada correctamente.');
    }

    public function desactivar(int $id)
    {
        $this->promociones->update($id, ['activo' => false]);

        return redirect()->back()->with('success', 'Promoción desactivada.');
    }

    private function postDatos(): array
    {
        return [
            'nombre'       => $this->request->getPost('nombre'),
            'descripcion'  => $this->request->getPost('descripcion'),
            'descuento'    => $this->request->getPost('descuento'),
            'codigo'       => strtoupper((string) $this->request->getPost('codigo')),
            'fecha_inicio' => $this->request->getPost('fecha_inicio'),
            'fecha_fin'    => $this->request->getPost('fecha_fin'),
            'activo'       => true,
        ];
    }
}