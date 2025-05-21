<?php

namespace model\service;
require_once(__DIR__ . '/../../config/database.php');
require_once(__DIR__ . '/../repository/PlantasRepository.php');
require_once(__DIR__ . '/../entity/Planta.php');

use model\entity\Planta;
use model\repository\PlantasRepository;
use \PDOException;
use \Exception;

class PlantaService
{
    private PlantasRepository $plantaRepository;

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
            throw new Exception("Error al obtener la planta: " . $e->getMessage());
        }
    }

    public function getPlantasByHospitalId($hospitalId): array
    {
        try {
            return $this->plantaRepository->findByHospitalId($hospitalId);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las plantas del hospital: " . $e->getMessage());
        }
    }

    public function createPlanta($nombre, $hospitalId): bool
    {
        try {
            if (empty($nombre)) {
                throw new Exception("El nombre de la planta es obligatorio");
            }
            
            if (empty($hospitalId)) {
                throw new Exception("El ID del hospital es obligatorio");
            }
            
            $planta = new Planta(0, $nombre, $hospitalId);
            return $this->plantaRepository->save($planta);
        } catch (PDOException $e) {
            throw new Exception("Error al crear la planta: " . $e->getMessage());
        }
    }

    public function updatePlanta($id, $nombre, $hospitalId): bool
    {
        try {
            if (empty($id) || empty($nombre) || empty($hospitalId)) {
                throw new Exception("El ID, nombre y hospital de la planta son obligatorios");
            }
            
            $planta = new Planta($id, $nombre, $hospitalId);
            return $this->plantaRepository->update($planta);
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar la planta: " . $e->getMessage());
        }
    }

    public function deletePlanta($id): bool
    {
        try {
            if (empty($id)) {
                throw new Exception("El ID de la planta es obligatorio");
            }
            
            return $this->plantaRepository->deleteById($id);
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar la planta: " . $e->getMessage());
        }
    }
}
