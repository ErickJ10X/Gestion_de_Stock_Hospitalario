<?php

namespace controller;

use Exception;
use model\entity\Plantas;
use model\service\PlantaService;

require_once(__DIR__ . '/../model/service/PlantaService.php');
require_once(__DIR__ . '/../model/entity/Plantas.php');

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

    public function getByHospital($hospitalId): array
    {
        try {
            if (!is_numeric($hospitalId) || $hospitalId <= 0) {
                return ['error' => true, 'mensaje' => 'ID de hospital inválido'];
            }
            
            $plantas = $this->plantaService->getPlantasByHospitalId($hospitalId);
            return ['error' => false, 'plantas' => $plantas];
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function store($nombre, $idHospital): array
    {
        try {
            if (empty($nombre)) {
                return ['error' => true, 'mensaje' => 'El nombre de la planta es obligatorio'];
            }

            if (!is_numeric($idHospital) || $idHospital <= 0) {
                return ['error' => true, 'mensaje' => 'ID de hospital inválido'];
            }

            $planta = new Plantas(null, $nombre, $idHospital);
            $resultado = $this->plantaService->savePlanta($planta);
            
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Planta creada correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo crear la planta'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function update($id, $nombre, $idHospital): array
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de planta inválido'];
            }

            if (empty($nombre)) {
                return ['error' => true, 'mensaje' => 'El nombre de la planta es obligatorio'];
            }

            if (!is_numeric($idHospital) || $idHospital <= 0) {
                return ['error' => true, 'mensaje' => 'ID de hospital inválido'];
            }

            // Verificar que la planta existe
            $plantaExistente = $this->plantaService->getPlantaById($id);
            if (!$plantaExistente) {
                return ['error' => true, 'mensaje' => 'La planta no existe'];
            }

            $planta = new Plantas($id, $nombre, $idHospital);
            $resultado = $this->plantaService->updatePlanta($planta);
            
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Planta actualizada correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo actualizar la planta'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    public function destroy($id): array
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de planta inválido'];
            }
            
            // Verificar que la planta existe
            $plantaExistente = $this->plantaService->getPlantaById($id);
            if (!$plantaExistente) {
                return ['error' => true, 'mensaje' => 'La planta no existe'];
            }

            $resultado = $this->plantaService->deletePlanta($id);
            
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Planta eliminada correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo eliminar la planta'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }
}
