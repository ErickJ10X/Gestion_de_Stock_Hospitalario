<?php

namespace controller;

use Exception;
use model\service\PlantaService;

require_once(__DIR__ . '/../model/service/PlantaService.php');

class PlantaController
{
    private PlantaService $plantaService;

    public function __construct()
    {
        $this->plantaService = new PlantaService();
    }

    public function index(): array
    {
        try {
            return ['error' => false, 'plantas' => $this->plantaService->getAllPlantas()];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function getByHospital($hospital_id): array
    {
        try {
            if (!is_numeric($hospital_id) || $hospital_id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de hospital inválido'];
            }
            
            return ['error' => false, 'plantas' => $this->plantaService->getPlantasByHospital($hospital_id)];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function show($id): array
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de planta inválido'];
            }
            
            $planta = $this->plantaService->getPlantaById($id);
            if ($planta) {
                return ['error' => false, 'planta' => $planta];
            } else {
                return ['error' => true, 'mensaje' => 'Planta no encontrada'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }
}
