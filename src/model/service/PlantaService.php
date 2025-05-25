<?php

namespace model\service;

require_once __DIR__ . '/../repository/PlantasRepository.php';
require_once __DIR__ . '/../entity/Plantas.php';

use model\repository\PlantasRepository;
use model\entity\Plantas;
use Exception;

class PlantaService
{
    private PlantasRepository $plantasRepository;

    public function __construct()
    {
        $this->plantasRepository = new PlantasRepository();
    }

    public function getAllPlantas(): array
    {
        try {
            return $this->plantasRepository->findAll();
        } catch (Exception $e) {
            error_log("Error en PlantaService::getAllPlantas: " . $e->getMessage());
            throw new Exception("Error al obtener las plantas: " . $e->getMessage());
        }
    }

    public function getPlantaById($id): ?Plantas
    {
        try {
            return $this->plantasRepository->findById($id);
        } catch (Exception $e) {
            error_log("Error en PlantaService::getPlantaById: " . $e->getMessage());
            return null;
        }
    }

    public function getPlantasByHospitalId($hospitalId): array
    {
        try {
            return $this->plantasRepository->findByHospitalId($hospitalId);
        } catch (Exception $e) {
            error_log("Error en PlantaService::getPlantasByHospitalId: " . $e->getMessage());
            return [];
        }
    }

    public function savePlanta(Plantas $planta): bool
    {
        try {
            return $this->plantasRepository->save($planta);
        } catch (Exception $e) {
            error_log("Error en PlantaService::savePlanta: " . $e->getMessage());
            return false;
        }
    }

    public function updatePlanta(Plantas $planta): bool
    {
        try {
            return $this->plantasRepository->update($planta);
        } catch (Exception $e) {
            error_log("Error en PlantaService::updatePlanta: " . $e->getMessage());
            return false;
        }
    }

    public function deletePlanta($id): bool
    {
        try {
            return $this->plantasRepository->delete($id);
        } catch (Exception $e) {
            error_log("Error en PlantaService::deletePlanta: " . $e->getMessage());
            return false;
        }
    }
}
