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
    
    /**
     * Crea una nueva planta
     * @param string $nombre Nombre de la planta
     * @param int $idHospital ID del hospital al que pertenece
     * @return array Resultado de la operación
     */
    public function create($nombre, $idHospital): array
    {
        try {
            if (empty(trim($nombre))) {
                return ['error' => true, 'mensaje' => 'El nombre de la planta es obligatorio'];
            }

            if (!is_numeric($idHospital) || $idHospital <= 0) {
                return ['error' => true, 'mensaje' => 'ID de hospital inválido'];
            }

            $resultado = $this->plantaService->createPlanta($nombre, $idHospital);
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Planta creada correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo crear la planta'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }
    
    /**
     * Actualiza una planta existente
     * @param int $id ID de la planta
     * @param string $nombre Nuevo nombre de la planta
     * @param int $idHospital Nuevo ID del hospital
     * @return array Resultado de la operación
     */
    public function update($id, $nombre, $idHospital): array
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de planta inválido'];
            }

            if (empty(trim($nombre))) {
                return ['error' => true, 'mensaje' => 'El nombre de la planta es obligatorio'];
            }

            if (!is_numeric($idHospital) || $idHospital <= 0) {
                return ['error' => true, 'mensaje' => 'ID de hospital inválido'];
            }

            $resultado = $this->plantaService->updatePlanta($id, $nombre, $idHospital);
            if ($resultado) {
                return ['error' => false, 'mensaje' => 'Planta actualizada correctamente'];
            } else {
                return ['error' => true, 'mensaje' => 'No se pudo actualizar la planta'];
            }
        } catch (Exception $e) {
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }
    
    /**
     * Elimina una planta
     * @param int $id ID de la planta a eliminar
     * @return array Resultado de la operación
     */
    public function delete($id): array
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return ['error' => true, 'mensaje' => 'ID de planta inválido'];
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
