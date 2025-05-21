<?php
namespace controller;

require_once(__DIR__ . '/../model/service/PlantaService.php');
require_once(__DIR__ . '/../model/entity/Planta.php');
include_once(__DIR__ . '/../util/Session.php');

use model\service\PlantaService;
use model\entity\Planta;
use util\Session;

class PlantaController
{
    private PlantaService $plantaService;
    private Session $session;

    public function __construct()
    {
        $this->plantaService = new PlantaService();
        $this->session = new Session();
    }

    public function getAllPlantas(): array
    {
        try {
            return $this->plantaService->getAllPlantas();
        } catch (Exception $e) {
            $this->handleError("Error al obtener plantas", $e->getMessage());
            return [];
        }
    }

    public function getPlantaById($id): ?Planta
    {
        try {
            if (empty($id)) {
                throw new Exception("El ID de la planta es requerido");
            }
            
            $planta = $this->plantaService->getPlantaById($id);
            
            if (!$planta) {
                throw new Exception("Planta no encontrada");
            }
            
            return $planta;
        } catch (Exception $e) {
            $this->handleError("Error al obtener planta", $e->getMessage());
            return null;
        }
    }

    public function getPlantasByHospitalId($hospitalId): array
    {
        try {
            if (empty($hospitalId)) {
                throw new Exception("El ID del hospital es requerido");
            }
            
            return $this->plantaService->getPlantasByHospitalId($hospitalId);
        } catch (Exception $e) {
            $this->handleError("Error al obtener plantas del hospital", $e->getMessage());
            return [];
        }
    }

    public function createPlanta($nombre, $hospitalId): bool
    {
        try {
            if (empty($nombre)) {
                throw new Exception("El nombre de la planta es requerido");
            }
            
            if (empty($hospitalId)) {
                throw new Exception("El ID del hospital es requerido");
            }
            
            if (!$this->plantaService->createPlanta($nombre, $hospitalId)) {
                throw new Exception("No se pudo crear la planta");
            }
            
            $this->session->setMessage("success", "Planta creada correctamente");
            return true;
        } catch (Exception $e) {
            $this->handleError("Error al crear planta", $e->getMessage());
            return false;
        }
    }

    public function updatePlanta($id, $nombre, $hospitalId): bool
    {
        try {
            if (empty($id) || empty($nombre) || empty($hospitalId)) {
                throw new Exception("El ID, nombre y hospital de la planta son requeridos");
            }
            
            $planta = $this->plantaService->getPlantaById($id);
            if (!$planta) {
                throw new Exception("Planta no encontrada");
            }
            
            if (!$this->plantaService->updatePlanta($id, $nombre, $hospitalId)) {
                throw new Exception("No se pudo actualizar la planta");
            }
            
            $this->session->setMessage("success", "Planta actualizada correctamente");
            return true;
        } catch (Exception $e) {
            $this->handleError("Error al actualizar planta", $e->getMessage());
            return false;
        }
    }

    public function deletePlanta($id): bool
    {
        try {
            if (empty($id)) {
                throw new Exception("El ID de la planta es requerido");
            }
            
            $planta = $this->plantaService->getPlantaById($id);
            if (!$planta) {
                throw new Exception("Planta no encontrada");
            }
            
            if (!$this->plantaService->deletePlanta($id)) {
                throw new Exception("No se pudo eliminar la planta");
            }
            
            $this->session->setMessage("success", "Planta eliminada correctamente");
            return true;
        } catch (Exception $e) {
            $this->handleError("Error al eliminar planta", $e->getMessage());
            return false;
        }
    }

    private function handleError($title, $message): void
    {
        $this->session->setMessage("error", $title . ": " . $message);
    }
}
