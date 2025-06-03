<?php

namespace controller;

use Exception;
use model\service\HospitalService;

require_once(__DIR__ . '/../model/service/HospitalService.php');
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
$hospitalApi = new HospitalApi();
$action = $_GET['action'] ?? '';

// Ejecutar la acción solicitada
try {
    switch ($action) {
        case 'getById':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            echo $hospitalApi->getHospitalById($id);
            break;
            
        case 'getAll':
            echo $hospitalApi->getAllHospitales();
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

class HospitalApi
{
    private HospitalService $hospitalService;

    public function __construct()
    {
        $this->hospitalService = new HospitalService();
    }

    /**
     * Obtiene un hospital por su ID y devuelve los datos en formato JSON
     * @param int $id ID del hospital
     * @return string JSON con los datos del hospital
     */
    public function getHospitalById(int $id): string
    {
        try {
            if ($id <= 0) {
                return json_encode([
                    'error' => true,
                    'mensaje' => 'ID de hospital inválido'
                ]);
            }

            $hospital = $this->hospitalService->getHospitalById($id);
            
            if (!$hospital) {
                return json_encode([
                    'error' => true,
                    'mensaje' => 'Hospital no encontrado'
                ]);
            }
            
            // Convertir el objeto hospital a un array asociativo para JSON
            $hospitalData = [
                'id_hospital' => $hospital->getIdHospital(),
                'nombre' => $hospital->getNombre(),
                'ubicacion' => $hospital->getUbicacion()
            ];
            
            return json_encode([
                'error' => false,
                'hospital' => $hospitalData
            ]);
            
        } catch (Exception $e) {
            return json_encode([
                'error' => true,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtiene todos los hospitales y devuelve los datos en formato JSON
     * @return string JSON con los datos de todos los hospitales
     */
    public function getAllHospitales(): string
    {
        try {
            $hospitales = $this->hospitalService->getAllHospitales();
            
            $hospitalesData = [];
            foreach ($hospitales as $hospital) {
                $hospitalesData[] = [
                    'id_hospital' => $hospital->getIdHospital(),
                    'nombre' => $hospital->getNombre(),
                    'ubicacion' => $hospital->getUbicacion()
                ];
            }
            
            return json_encode([
                'error' => false,
                'hospitales' => $hospitalesData
            ]);
            
        } catch (Exception $e) {
            return json_encode([
                'error' => true,
                'mensaje' => $e->getMessage()
            ]);
        }
    }
}
