<?php

namespace controller;

use Exception;
use model\service\AlmacenesService;

require_once(__DIR__ . '/../model/service/AlmacenesService.php');

class AlmacenesController
{
    private AlmacenesService $almacenesService;

    public function __construct()
    {
        $this->almacenesService = new AlmacenesService();
    }

    public function index(): array
    {
        try {
            return $this->almacenesService->getAllAlmacenes();
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function show($id): array
    {
        try {
            $almacen = $this->almacenesService->getAlmacenById($id);
            if ($almacen) {
                return ['error' => false, 'almacen' => $almacen];
            } else {
                return ['error' => true, 'mensaje' => 'Almacén no encontrado'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function store($planta_id, $tipo, $id_hospital): array
    {
        try {
            $resultado = $this->almacenesService->createAlmacen($planta_id, $tipo, $id_hospital);
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Almacén creado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo crear el almacén'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function update($id, $planta_id, $tipo, $id_hospital): array
    {
        try {
            $resultado = $this->almacenesService->updateAlmacen($id, $planta_id, $tipo, $id_hospital);
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Almacén actualizado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo actualizar el almacén'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function destroy($id): array
    {
        try {
            $resultado = $this->almacenesService->deleteAlmacen($id);
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Almacén eliminado correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo eliminar el almacén'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }
}
