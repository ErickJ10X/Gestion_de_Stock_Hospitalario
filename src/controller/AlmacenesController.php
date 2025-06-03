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

    public function getByPlanta($plantaId): array
    {
        try {
            if (!is_numeric($plantaId) || $plantaId <= 0) {
                return ['error' => true, 'mensaje' => 'ID de planta inválido'];
            }
            
            return $this->almacenesService->getAlmacenesByPlanta($plantaId);
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function show($id): array
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de almacén inválido'];
            }
            
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
}
