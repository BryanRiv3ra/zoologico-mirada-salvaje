<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Model;

class ApiCrud extends ResourceController
{
    protected $format = 'json';
    private const MAX_JSON_BYTES = 1048576;

    private const MODELS = [
        'especies' => \App\Models\EspecieModel::class,
        'zonas' => \App\Models\ZonaModel::class,
        'animales' => \App\Models\AnimalModel::class,
        'empleados' => \App\Models\EmpleadoModel::class,
        'usuarios' => \App\Models\UsuarioModel::class,
        'roles' => \App\Models\RolModel::class,
        'usuario-rol' => \App\Models\UsuarioRolModel::class,
        'proveedores' => \App\Models\ProveedorModel::class,
        'inventario' => \App\Models\InventarioModel::class,
        'movimientos-inventario' => \App\Models\MovimientoInventarioModel::class,
        'dietas' => \App\Models\DietaModel::class,
        'dieta-detalle' => \App\Models\DietaDetalleModel::class,
        'horarios-alimentacion' => \App\Models\HorarioAlimentacionModel::class,
        'registros-alimentacion' => \App\Models\RegistroAlimentacionModel::class,
        'tareas-limpieza' => \App\Models\TareaLimpiezaModel::class,
        'registros-limpieza' => \App\Models\RegistroLimpiezaModel::class,
        'vacunas' => \App\Models\VacunaModel::class,
        'historial-clinico' => \App\Models\HistorialClinicoModel::class,
        'tratamientos' => \App\Models\TratamientoModel::class,
        'aplicaciones-vacunas' => \App\Models\AplicacionVacunaModel::class,
        'eventos' => \App\Models\EventoModel::class,
        'promociones' => \App\Models\PromocionModel::class,
        'tarifas' => \App\Models\TarifaModel::class,
        'visitantes' => \App\Models\VisitanteModel::class,
        'entradas' => \App\Models\EntradaModel::class,
        'pagos' => \App\Models\PagoModel::class,
    ];

    public function index(string $resource): ResponseInterface
    {
        $model = $this->modelFor($resource);
        if ($model === null) {
            return $this->failNotFound('Recurso no encontrado.');
        }

        return $this->respond($this->hideSensitiveData($resource, $model->findAll()));
    }

    public function show(string $resource, int $id): ResponseInterface
    {
        $model = $this->modelFor($resource);
        if ($model === null) {
            return $this->failNotFound('Recurso no encontrado.');
        }

        $record = $model->find($id);
        return $record === null
            ? $this->failNotFound('Registro no encontrado.')
            : $this->respond($this->hideSensitiveData($resource, $record));
    }

    public function create(string $resource): ResponseInterface
    {
        $model = $this->modelFor($resource);
        if ($model === null) {
            return $this->failNotFound('Recurso no encontrado.');
        }

        $data = $this->jsonData();
        if (! is_array($data)) {
            return $this->failValidationErrors('El cuerpo debe ser un objeto JSON.');
        }

        if (! $model->insert($data)) {
            return $this->failValidationErrors($model->errors());
        }

        return $this->respondCreated($this->hideSensitiveData($resource, $model->find($model->getInsertID())));
    }

    public function update(string $resource, int $id): ResponseInterface
    {
        $model = $this->modelFor($resource);
        if ($model === null) {
            return $this->failNotFound('Recurso no encontrado.');
        }

        if ($model->find($id) === null) {
            return $this->failNotFound('Registro no encontrado.');
        }

        $data = $this->jsonData();
        if (! is_array($data)) {
            return $this->failValidationErrors('El cuerpo debe ser un objeto JSON.');
        }

        if (! $model->update($id, $data)) {
            return $this->failValidationErrors($model->errors());
        }

        return $this->respond($this->hideSensitiveData($resource, $model->find($id)));
    }

    public function delete(string $resource, int $id): ResponseInterface
    {
        $model = $this->modelFor($resource);
        if ($model === null) {
            return $this->failNotFound('Recurso no encontrado.');
        }

        if ($model->find($id) === null) {
            return $this->failNotFound('Registro no encontrado.');
        }

        $model->delete($id);
        return $this->respondDeleted(['id' => $id]);
    }

    private function modelFor(string $resource): ?Model
    {
        if (! isset(self::MODELS[$resource])) {
            return null;
        }

        $class = self::MODELS[$resource];
        return new $class();
    }

    private function jsonData(): mixed
    {
        $body = $this->request->getBody();
        if (strlen($body) > self::MAX_JSON_BYTES) {
            return null;
        }

        return $this->request->getJSON(true);
    }

    private function hideSensitiveData(string $resource, mixed $data): mixed
    {
        if ($resource === 'usuarios' && is_array($data)) {
            if (array_is_list($data)) {
                foreach ($data as &$record) {
                    unset($record['password_hash'], $record['password']);
                }
            } else {
                unset($data['password_hash'], $data['password']);
            }
        }

        return $data;
    }
}
