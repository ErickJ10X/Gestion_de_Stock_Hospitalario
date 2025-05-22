<?php

namespace controller;

require_once __DIR__ . '/../model/entity/Planta.php';
require_once __DIR__ . '/../model/service/PlantaService.php';
require_once __DIR__ . '/../model/repository/PlantasRepository.php';

use Exception;
use model\entity\Planta;
use model\service\PlantaService;

class PlantaController
{
    private $plantaService;

    public function __construct()
    {
        $this->plantaService = new PlantaService();
    }

    public function getAllPlantas()
    {
        try {
            return $this->plantaService->getAllPlantas();
        } catch (Exception $e) {
            error_log("Error en PlantaController::getAllPlantas: " . $e->getMessage());
            throw new Exception("Error al obtener las plantas: " . $e->getMessage());
        }
    }

    public function getPlantaById($id)
    {
        try {
            return $this->plantaService->getPlantaById($id);
        } catch (Exception $e) {
            error_log("Error en PlantaController::getPlantaById: " . $e->getMessage());
            return null;
        }
    }

    public function getPlantasByHospitalId($hospitalId)
    {
        try {
            return $this->plantaService->getPlantasByHospitalId($hospitalId);
        } catch (Exception $e) {
            error_log("Error en PlantaController::getPlantasByHospitalId: " . $e->getMessage());
            return [];
        }
    }

    public function createPlanta($nombre, $hospitalId)
    {
        try {
            $planta = new Planta();
            $planta->setNombre($nombre);
            $planta->setHospitalId($hospitalId);
            return $this->plantaService->savePlanta($planta);
        } catch (Exception $e) {
            error_log("Error en PlantaController::createPlanta: " . $e->getMessage());
            return false;
        }
    }

    public function updatePlanta($id, $nombre, $hospitalId)
    {
        try {
            $planta = $this->plantaService->getPlantaById($id);
            if ($planta) {
                $planta->setNombre($nombre);
                $planta->setHospitalId($hospitalId);
                return $this->plantaService->updatePlanta($planta);
            }
            return false;
        } catch (Exception $e) {
            error_log("Error en PlantaController::updatePlanta: " . $e->getMessage());
            return false;
        }
    }

    public function deletePlanta($id)
    {
        try {
            return $this->plantaService->deletePlanta($id);
        } catch (Exception $e) {
            error_log("Error en PlantaController::deletePlanta: " . $e->getMessage());
            return false;
        }
    }
}
