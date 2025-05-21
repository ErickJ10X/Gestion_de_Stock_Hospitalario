<?php

namespace model\service;

use model\entity\Planta;
use model\repository\PlantasRepository;
use PDOException;
use Exception;

require_once(__DIR__ . '/../repository/PlantasRepository.php');

class PlantaService
{
    private $plantaRepository;

    public function __construct()
    {
        $this->plantaRepository = new PlantasRepository();
    }

    public function getAllPlantas(): array
    {
        try {
            return $this->plantaRepository->findAll();
        } catch (PDOException $e) {
            throw new Exception("Error al cargar las plantas: " . $e->getMessage());
        }
    }

    public function getPlantaById($id): ?Planta
    {
        try {
            return $this->plantaRepository->findById($id);
        } catch (PDOException $e) {
            throw new Exception("Error al cargar la planta: " . $e->getMessage());
        }
    }
    
    public function getPlantasByHospitalId($hospitalId): array
    {
        try {
            return $this->plantaRepository->findByHospitalId($hospitalId);
        } catch (PDOException $e) {
            throw new Exception("Error al cargar las plantas del hospital: " . $e->getMessage());
        }
    }
    
    public function createPlanta($nombre, $hospitalId): bool
    {
        try {
            $planta = new Planta(null, $nombre, $hospitalId);
            return $this->plantaRepository->save($planta);
        } catch (PDOException $e) {
            throw new Exception("Error al crear la planta: " . $e->getMessage());
        }
    }
    
    public function updatePlanta($id, $nombre, $hospitalId): bool
    {
        try {
            $planta = new Planta($id, $nombre, $hospitalId);
            return $this->plantaRepository->update($planta);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar la planta: " . $e->getMessage());
        }
    }
    
    public function deletePlanta($id): bool
    {
        try {
            return $this->plantaRepository->deleteById($id);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar la planta: " . $e->getMessage());
        }
    }
}
