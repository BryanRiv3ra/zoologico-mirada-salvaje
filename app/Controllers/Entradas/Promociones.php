<?php

namespace App\Controllers\Entradas;

use App\Controllers\BaseController;
use App\Models\Entradas\PromocionModel;
use App\Models\Entradas\PromocionTarifaModel;
use App\Models\Entradas\TarifaModel;

/**
 * Gestión de promociones. Permiso: admin_mercadeo, administrador.
 */
class Promociones extends BaseController
{
    protected $helpers = ['url', 'form'];

    public function index(): string
    {
        $promos = model(PromocionModel::class)->todas();
        $tarifaModel = model(TarifaModel::class);

        $detalle = [];
        foreach ($promos as $promo) {
            $tarifas = model(PromocionTarifaModel::class)
                ->select('tarifa_id')
                ->where('promocion_id', $promo['id'])
                ->findAll();
            $nombres = [];
            foreach ($tarifas as $relacion) {
                $tarifa = $tarifaModel->find((int) $relacion['tarifa_id']);
                if ($tarifa !== null) {
                    $nombres[] = $tarifa['nombre'];
                }
            }
            $detalle[] = ['promocion' => $promo, 'tarifas' => $nombres];
        }

        return view('entradas/promociones/index', [
            'titulo'   => 'Promociones',
            'cssExtra' => 'entradas.css',
            'promos'   => $detalle,
        ]);
    }

    public function nueva(): string
    {
        return view('entradas/promociones/form', [
            'titulo'   => 'Nueva promoción',
            'cssExtra' => 'entradas.css',
            'promo'    => null,
            'tarifas'  => model(TarifaModel::class)->todas(),
            'seleccionadas' => [],
        ]);
    }

    public function guardar()
    {
        $datos = $this->postDatos();
        $tarifas = (array) $this->request->getPost('tarifas');

        if ($tarifas === []) {
            return redirect()->back()->withInput()->with('error', 'Debes asociar al menos una tarifa a la promoción.');
        }

        if (! model(PromocionModel::class)->validate($datos)) {
            return redirect()->back()->withInput()->with('errors', model(PromocionModel::class)->errors());
        }

        $promoId = (int) model(PromocionModel::class)->insert($datos);
        model(PromocionTarifaModel::class)->reemplazar($promoId, $tarifas);

        return redirect()->to('/entradas/promociones')->with('success', 'Promoción creada correctamente.');
    }

    public function editar(int $id): string
    {
        $promo = model(PromocionModel::class)->conTarifas($id);
        if ($promo === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('entradas/promociones/form', [
            'titulo'       => 'Editar promoción',
            'cssExtra'     => 'entradas.css',
            'promo'        => $promo,
            'tarifas'      => model(TarifaModel::class)->todas(),
            'seleccionadas' => array_map('intval', array_column($promo['tarifas'], 'id')),
        ]);
    }

    public function actualizar(int $id)
    {
        $promo = model(PromocionModel::class)->find($id);
        if ($promo === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $datos = $this->postDatos();
        $tarifas = (array) $this->request->getPost('tarifas');

        if ($tarifas === []) {
            return redirect()->back()->withInput()->with('error', 'Debes asociar al menos una tarifa a la promoción.');
        }

        if (! model(PromocionModel::class)->validate($datos)) {
            return redirect()->back()->withInput()->with('errors', model(PromocionModel::class)->errors());
        }

        model(PromocionModel::class)->update($id, $datos);
        model(PromocionTarifaModel::class)->reemplazar($id, $tarifas);

        return redirect()->to('/entradas/promociones')->with('success', 'Promoción actualizada correctamente.');
    }

    public function desactivar(int $id)
    {
        model(PromocionModel::class)->update($id, ['activo' => false]);

        return redirect()->back()->with('success', 'Promoción desactivada.');
    }

    private function postDatos(): array
    {
        return [
            'nombre'       => $this->request->getPost('nombre'),
            'descripcion'  => $this->request->getPost('descripcion'),
            'descuento'    => $this->request->getPost('descuento'),
            'codigo'       => $this->request->getPost('codigo'),
            'fecha_inicio' => $this->request->getPost('fecha_inicio'),
            'fecha_fin'    => $this->request->getPost('fecha_fin'),
            'activo'       => true,
        ];
    }
}