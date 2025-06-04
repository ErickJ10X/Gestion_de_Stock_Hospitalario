<?php

namespace controller;

use model\service\PlantaService;
use model\service\HospitalService;
use model\entity\Planta;
use util\Redirect;
use util\Session;
use util\AuthGuard;
use Exception;

require_once(__DIR__ . '/../model/service/PlantaService.php');
require_once(__DIR__ . '/../model/service/HospitalService.php');
require_once(__DIR__ . '/../model/repository/PlantaRepository.php');
require_once(__DIR__ . '/../model/entity/Planta.php');
require_once(__DIR__ . '/../util/Session.php');
require_once(__DIR__ . '/../util/AuthGuard.php');

class PlantaController {
    private PlantaService $plantaService;
    private HospitalService $hospitalService;
    private Session $session;
    private AuthGuard $authGuard;

    public function __construct() {
        $this->plantaService = new PlantaService();
        $this->hospitalService = new HospitalService();
        $this->session = new Session();
        $this->authGuard = new AuthGuard();
    }

    /**
     * Método principal para obtener los datos utilizados en la vista index
     */
    public function index(): array {
        $this->authGuard->requireGestorHospital();
        
        $viewData = [
            'plantas' => []
        ];

        try {
            $viewData['plantas'] = $this->plantaService->getAllPlantas();
            return $viewData;
        } catch (Exception $e) {
            $this->session->setMessage('error', "Error al cargar plantas: " . $e->getMessage());
            return $viewData;
        }
    }

    /**
     * Obtiene una planta por su ID
     */
    public function getById(int $id): array {
        try {
            $planta = $this->plantaService->getPlantaById($id);
            
            if ($planta) {
                return [
                    'error' => false,
                    'planta' => $planta->toArray()
                ];
            } else {
                return [
                    'error' => true,
                    'mensaje' => 'Planta no encontrada'
                ];
            }
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene plantas por hospital
     */
    public function getByHospital(int $idHospital): array {
        try {
            $plantas = $this->plantaService->getPlantasByHospital($idHospital);
            
            return [
                'error' => false,
                'plantas' => $plantas
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'mensaje' => $e->getMessage(),
                'plantas' => []
            ];
        }
    }

    /**
     * Crea una nueva planta
     */
    public function crear(): void {
        $this->authGuard->requireGestorHospital();
        
        try {
            // Validar datos
            $nombre = $_POST['nombre'] ?? '';
            $idHospital = isset($_POST['id_hospital']) ? (int)$_POST['id_hospital'] : 0;
            
            if (empty($nombre)) {
                throw new Exception("El nombre de la planta es obligatorio");
            }
            
            if ($idHospital <= 0) {
                throw new Exception("Debe seleccionar un hospital válido");
            }
            
            // Verificar que el hospital existe
            $hospital = $this->hospitalService->getHospitalById($idHospital);
            if (!$hospital) {
                throw new Exception("El hospital seleccionado no existe");
            }
            
            // Crear planta
            $planta = $this->plantaService->createPlanta([
                'nombre' => $nombre,
                'id_hospital' => $idHospital
            ]);
            
            $this->session->setMessage('success', "Planta creada correctamente");
            Redirect::toHospitales();
        } catch (Exception $e) {
            $this->session->setMessage('error', $e->getMessage());
            $this->redirect('../hospitales/index.php?tab=agregar-editar');
        }
    }

    /**
     * Actualiza una planta existente
     */
    public function editar(): void {
        $this->authGuard->requireGestorHospital();
        
        try {
            // Validar datos
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $nombre = $_POST['nombre'] ?? '';
            $idHospital = isset($_POST['id_hospital']) ? (int)$_POST['id_hospital'] : 0;
            
            if ($id <= 0) {
                throw new Exception("ID de planta no válido");
            }
            
            if (empty($nombre)) {
                throw new Exception("El nombre de la planta es obligatorio");
            }
            
            if ($idHospital <= 0) {
                throw new Exception("Debe seleccionar un hospital válido");
            }
            
            // Verificar que el hospital existe
            $hospital = $this->hospitalService->getHospitalById($idHospital);
            if (!$hospital) {
                throw new Exception("El hospital seleccionado no existe");
            }
            
            // Actualizar planta
            $planta = $this->plantaService->updatePlanta($id, [
                'nombre' => $nombre,
                'id_hospital' => $idHospital
            ]);
            
            $this->session->setMessage('success', "Planta actualizada correctamente");
            $this->redirect('../hospitales/index.php');
        } catch (Exception $e) {
            $this->session->setMessage('error', $e->getMessage());
            $this->redirect('../hospitales/index.php?tab=agregar-editar');
        }
    }

    /**
     * Elimina una planta
     */
    public function eliminar(): void {
        $this->authGuard->requireHospitalGestor();
        
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            
            if ($id <= 0) {
                throw new Exception("ID de planta no válido");
            }
            
            // TODO: Verificar si hay botiquines o almacenes asociados a la planta
            
            // Eliminar planta
            $eliminado = $this->plantaService->deletePlanta($id);
            
            if ($eliminado) {
                $this->session->setMessage('success', "Planta eliminada correctamente");
            } else {
                $this->session->setMessage('error', "Error al eliminar la planta");
            }
            
            $this->redirect('../hospitales/index.php');
        } catch (Exception $e) {
            $this->session->setMessage('error', $e->getMessage());
            $this->redirect('../hospitales/index.php');
        }
    }

    /**
     * Procesa una solicitud desde la vista API
     */
    public function processApiRequest(): void {
        header('Content-Type: application/json');
        
        try {
            $this->authGuard->requireHospitalGestor();
            
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $action = $_GET['action'] ?? '';
                
                switch ($action) {
                    case 'getById':
                        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                        echo json_encode($this->getById($id));
                        break;
                    case 'getByHospital':
                        $idHospital = isset($_GET['idHospital']) ? (int)$_GET['idHospital'] : 0;
                        echo json_encode($this->getByHospital($idHospital));
                        break;
                    default:
                        echo json_encode([
                            'error' => true,
                            'mensaje' => 'Acción no reconocida'
                        ]);
                        break;
                }
            } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $action = $_POST['action'] ?? '';
                
                switch ($action) {
                    case 'crear':
                        $this->crear();
                        break;
                    case 'editar':
                        $this->editar();
                        break;
                    case 'eliminar':
                        $this->eliminar();
                        break;
                    default:
                        echo json_encode([
                            'error' => true,
                            'mensaje' => 'Acción no reconocida'
                        ]);
                        break;
                }
            }
        } catch (Exception $e) {
            echo json_encode([
                'error' => true,
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    /**
     * Redirige a una URL relativa
     */
    private function redirect(string $path): void {
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/' . $path);
        exit();
    }

    /**
     * Procesa la solicitud actual
     */
    public function processRequest(): void {
        // Determinar si es una solicitud API
        $isApiRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
                        
        if ($isApiRequest) {
            $this->processApiRequest();
            return;
        }

        // Procesar solicitudes normales
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'crear':
                    $this->crear();
                    break;
                case 'editar':
                    $this->editar();
                    break;
                case 'eliminar':
                    $this->eliminar();
                    break;
                default:
                    $this->session->setMessage('error', 'Acción no válida');
                    $this->redirect('../hospitales/index.php');
                    break;
            }
        }
    }
}

// Ejecutar el controlador si este archivo es llamado directamente
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    $controller = new PlantaController();
    $controller->processRequest();
}
