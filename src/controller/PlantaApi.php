<?php

namespace controller;

use Exception;
use model\service\PlantaService;

require_once(__DIR__ . '/../model/service/PlantaService.php');
include_once(__DIR__ . '/../util/AuthGuard.php');

use util\AuthGuard;

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar permisos
$authGuard = new AuthGuard();
$authGuard->requireHospitalGestor();

// Configurar cabeceras para JSON
header('Content-Type: application/json; charset=utf-8');

// Procesar la petición
$plantaApi = new PlantaApi();
$action = $_GET['action'] ?? '';

// Ejecutar la acción solicitada
try {
    switch ($action) {
        case 'getById':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            echo $plantaApi->getPlantaById($id);
            break;
            
        case 'getByHospital':
            $hospitalId = isset($_GET['hospitalId']) ? (int)$_GET['hospitalId'] : 0;
            echo $plantaApi->getPlantasByHospital($hospitalId);
            break;
            
        case 'getAll':
            echo $plantaApi->getAllPlantas();
            break;
            
        default:
            echo json_encode([
                'error' => true,
                'mensaje' => 'Acción no válida'
            ]);
            break;
    }
} catch (Exception $e) {
    echo json_encode([
        'error' => true,
        'mensaje' => $e->getMessage()
    ]);
}

class PlantaApi
{
    private PlantaService $plantaService;

    public function __construct()
    {
        $this->plantaService = new PlantaService();
    }

    /**
     * Obtiene una planta por su ID y devuelve los datos en formato JSON
     * @param int $id ID de la planta
     * @return string JSON con los datos de la planta
     */
    public function getPlantaById(int $id): string
    {
        try {
            if ($id <= 0) {
                return json_encode([
                    'error' => true,
                    'mensaje' => 'ID de planta inválido'
                ]);
            }

            $planta = $this->plantaService->getPlantaById($id);
            
            if (!$planta) {
                return json_encode([
                    'error' => true,
                    'mensaje' => 'Planta no encontrada'
                ]);
            }
            
            // Convertir el objeto planta a un array asociativo para JSON
            $plantaData = [
                'id_planta' => $planta->getIdPlanta(),
                'nombre' => $planta->getNombre(),
                'id_hospital' => $planta->getIdHospital()
            ];
            
            return json_encode([
                'error' => false,
                'planta' => $plantaData
            ]);
            
        } catch (Exception $e) {
            return json_encode([
                'error' => true,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtiene todas las plantas de un hospital específico
     * @param int $hospitalId ID del hospital
     * @return string JSON con los datos de las plantas
     */
    public function getPlantasByHospital(int $hospitalId): string
    {
        try {
            if ($hospitalId <= 0) {
                return json_encode([
                    'error' => true,
                    'mensaje' => 'ID de hospital inválido'
                ]);
            }
            
            $plantas = $this->plantaService->getPlantasByHospital($hospitalId);
            
            $plantasData = [];
            foreach ($plantas as $planta) {
                $plantasData[] = [
                    'id_planta' => $planta->getIdPlanta(),
                    'nombre' => $planta->getNombre(),
                    'id_hospital' => $planta->getIdHospital()
                ];
            }
            
            return json_encode([
                'error' => false,
                'plantas' => $plantasData
            ]);
            
        } catch (Exception $e) {
            return json_encode([
                'error' => true,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtiene todas las plantas y devuelve los datos en formato JSON
     * @return string JSON con los datos de todas las plantas
     */
    public function getAllPlantas(): string
    {
        try {
            $plantas = $this->plantaService->getAllPlantas();
            
            $plantasData = [];
            foreach ($plantas as $planta) {
                $plantasData[] = [
                    'id_planta' => $planta->getIdPlanta(),
                    'nombre' => $planta->getNombre(),
                    'id_hospital' => $planta->getIdHospital()
                ];
            }
            
            return json_encode([
                'error' => false,
                'plantas' => $plantasData
            ]);
            
        } catch (Exception $e) {
            return json_encode([
                'error' => true,
                'mensaje' => $e->getMessage()
            ]);
        }
    }
}
