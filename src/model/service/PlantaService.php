<?php

namespace model\service;

require_once __DIR__ . '/../repository/PlantasRepository.php';
require_once __DIR__ . '/../entity/Planta.php';
require_once __DIR__ . '/../../config/database.php';

use config\Database;
use model\repository\PlantasRepository;
use model\entity\Planta;
use Exception;
use PDO;

class PlantaService
{
    private $plantasRepository;
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->plantasRepository = new PlantasRepository($this->db);
    }

    public function getAllPlantas()
    {
        try {
            return $this->plantasRepository->findAll();
        } catch (Exception $e) {
            error_log("Error en PlantaService::getAllPlantas: " . $e->getMessage());
            throw new Exception("Error al obtener las plantas: " . $e->getMessage());
        }
    }

    public function getPlantaById($id)
    {
        try {
            return $this->plantasRepository->findById($id);
        } catch (Exception $e) {
            error_log("Error en PlantaService::getPlantaById: " . $e->getMessage());
            return null;
        }
    }

    public function getPlantasByHospitalId($hospitalId)
    {
        try {
            return $this->plantasRepository->findByHospitalId($hospitalId);
        } catch (Exception $e) {
            error_log("Error en PlantaService::getPlantasByHospitalId: " . $e->getMessage());
            return [];
        }
    }

    public function savePlanta(Planta $planta)
    {
        try {
            return $this->plantasRepository->save($planta);
        } catch (Exception $e) {
            error_log("Error en PlantaService::savePlanta: " . $e->getMessage());
            return false;
        }
    }

    public function updatePlanta(Planta $planta)
    {
        try {
            return $this->plantasRepository->update($planta);
        } catch (Exception $e) {
            error_log("Error en PlantaService::updatePlanta: " . $e->getMessage());
            return false;
        }
    }

    public function deletePlanta($id)
    {
        try {
            return $this->plantasRepository->delete($id);
        } catch (Exception $e) {
            error_log("Error en PlantaService::deletePlanta: " . $e->getMessage());
            return false;
        }
    }
}
