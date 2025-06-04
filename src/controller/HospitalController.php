<?php

namespace controller;

use model\service\HospitalService;
use model\service\PlantaService;
use model\entity\Hospital;
use model\entity\Planta;
use util\Session;
use util\AuthGuard;
use Exception;

require_once(__DIR__ . '/../model/service/HospitalService.php');
require_once(__DIR__ . '/../model/service/PlantaService.php');
require_once(__DIR__ . '/../model/repository/HospitalRepository.php');
require_once(__DIR__ . '/../model/repository/PlantaRepository.php');
require_once(__DIR__ . '/../model/entity/Hospital.php');
require_once(__DIR__ . '/../model/entity/Planta.php');
require_once(__DIR__ . '/../util/Session.php');
require_once(__DIR__ . '/../util/AuthGuard.php');

class HospitalController {
    private HospitalService $hospitalService;
    private PlantaService $plantaService;
    private Session $session;
    private AuthGuard $authGuard;

    public function __construct() {
        $this->hospitalService = new HospitalService();
        $this->plantaService = new PlantaService();
        $this->session = new Session();
        $this->authGuard = new AuthGuard();
    }

    /**
     * Método principal para obtener los datos utilizados en la vista index
     */
    public function index(): array {
        $this->authGuard->requireGestorHospital();
        
        $viewData = [
            'hospitales' => [],
            'plantas' => []
        ];

        try {
            $viewData['hospitales'] = $this->hospitalService->getAllHospitales();
            $viewData['plantas'] = $this->plantaService->getAllPlantas();
            
            // Procesar cualquier mensaje de sesión
            $this->procesarMensajes();
            
            return $viewData;
        } catch (Exception $e) {
            $this->session->setMessage('error', "Error al cargar datos: " . $e->getMessage());
            return $viewData;
        }
    }

    /**
     * Obtiene un hospital por su ID
     */
    public function getById(int $id): array {
        try {
            $hospital = $this->hospitalService->getHospitalById($id);
            
            if ($hospital) {
                return [
                    'error' => false,
                    'hospital' => $hospital->toArray()
                ];
            } else {
                return [
                    'error' => true,
                    'mensaje' => 'Hospital no encontrado'
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
     * Obtiene las plantas asociadas a un hospital
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
     * Crea un nuevo hospital
     */
    public function crear(): void {
        $this->authGuard->requireHospitalGestor();
        
        try {
            // Validar datos
            $nombre = $_POST['nombre'] ?? '';
            $ubicacion = $_POST['ubicacion'] ?? '';
            
            if (empty($nombre)) {
                throw new Exception("El nombre del hospital es obligatorio");
            }
            
            // Crear hospital
            $hospital = $this->hospitalService->createHospital([
                'nombre' => $nombre,
                'ubicacion' => $ubicacion
            ]);
            
            $this->session->setMessage('success', "Hospital creado correctamente");
            $this->redirect('index.php');
        } catch (Exception $e) {
            $this->session->setMessage('error', $e->getMessage());
            $this->redirect('index.php?tab=agregar-editar');
        }
    }

    /**
     * Actualiza un hospital existente
     */
    public function editar(): void {
        $this->authGuard->requireHospitalGestor();
        
        try {
            // Validar datos
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $nombre = $_POST['nombre'] ?? '';
            $ubicacion = $_POST['ubicacion'] ?? '';
            
            if ($id <= 0) {
                throw new Exception("ID de hospital no válido");
            }
            
            if (empty($nombre)) {
                throw new Exception("El nombre del hospital es obligatorio");
            }
            
            // Actualizar hospital
            $hospital = $this->hospitalService->updateHospital($id, [
                'nombre' => $nombre,
                'ubicacion' => $ubicacion
            ]);
            
            $this->session->setMessage('success', "Hospital actualizado correctamente");
            $this->redirect('index.php');
        } catch (Exception $e) {
            $this->session->setMessage('error', $e->getMessage());
            $this->redirect('index.php?tab=agregar-editar');
        }
    }

    /**
     * Elimina un hospital
     */
    public function eliminar(): void {
        $this->authGuard->requireHospitalGestor();
        
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            
            if ($id <= 0) {
                throw new Exception("ID de hospital no válido");
            }
            
            // Primero verificar si hay plantas asociadas
            $plantas = $this->plantaService->getPlantasByHospital($id);
            if (!empty($plantas)) {
                throw new Exception("No se puede eliminar el hospital porque tiene plantas asociadas");
            }
            
            // Eliminar hospital
            $eliminado = $this->hospitalService->deleteHospital($id);
            
            if ($eliminado) {
                $this->session->setMessage('success', "Hospital eliminado correctamente");
            } else {
                $this->session->setMessage('error', "Error al eliminar el hospital");
            }
            
            $this->redirect('index.php');
        } catch (Exception $e) {
            $this->session->setMessage('error', $e->getMessage());
            $this->redirect('index.php');
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
     * Procesa cualquier mensaje en la sesión
     */
    private function procesarMensajes(): void {
        // Los mensajes se manejan automáticamente en la vista
    }

    /**
     * Redirige a una URL relativa al módulo de hospitales
     */
    private function redirect(string $path): void {
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/' . $path);
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
                    $this->redirect('index.php');
                    break;
            }
        }
    }
}

// Ejecutar el controlador si este archivo es llamado directamente
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    $controller = new HospitalController();
    $controller->processRequest();
}
