<?php

namespace controller;

use Exception;
use model\entity\Botiquin;
use model\service\BotiquinService;
use model\repository\BotiquinRepository;
use util\Session;
use util\AuthGuard;

require_once(__DIR__ . '/../model/service/BotiquinService.php');
require_once(__DIR__ . '/../model/repository/BotiquinRepository.php');
require_once(__DIR__ . '/../model/entity/Botiquin.php');
require_once(__DIR__ . '/../util/Session.php');
require_once(__DIR__ . '/../util/AuthGuard.php');

class BotiquinController
{
    private BotiquinService $botiquinService;
    private Session $session;
    private AuthGuard $authGuard;

    public function __construct()
    {
        $this->botiquinService = new BotiquinService(new BotiquinRepository());
        $this->session = new Session();
        $this->authGuard = new AuthGuard();
    }

    /**
     * Método principal para obtener los botiquines
     * @return array Datos para la vista de botiquines
     */
    public function index(): array
    {
        $this->authGuard->requireGestorGeneral();
        
        $viewData = [
            'botiquines' => []
        ];

        try {
            $viewData['botiquines'] = $this->botiquinService->getAllBotiquines();
            return $viewData;
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al cargar los botiquines: ' . $e->getMessage());
            return $viewData;
        }
    }

    /**
     * Obtiene botiquines por planta
     * @param int $plantaId ID de la planta
     * @return array Lista de botiquines de una planta específica
     */
    public function getByPlanta(int $plantaId): array
    {
        try {
            return $this->botiquinService->getBotiquinesByPlanta($plantaId);
        } catch (Exception ) {
            return [];
        }
    }

    /**
     * Obtiene botiquines por hospital
     * @param int $hospitalId ID del hospital
     * @return array Lista de botiquines de un hospital específico
     */
    public function getByHospital(int $hospitalId): array
    {
        try {
            return $this->botiquinService->getBotiquinesByHospital($hospitalId);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtiene un botiquín por su ID
     * @param int $id ID del botiquín
     * @return Botiquin|null El botiquín encontrado o null
     */
    public function getById(int $id): ?Botiquin
    {
        try {
            return $this->botiquinService->getBotiquinById($id);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Crea un nuevo botiquín
     */
    public function crear(): void
    {
        $this->authGuard->requireGestorPlanta();
        
        try {
            $plantaId = isset($_POST['planta_id']) ? (int)$_POST['planta_id'] : 0;
            $nombre = $_POST['nombre'] ?? '';
            
            // Validaciones
            if ($plantaId <= 0) {
                throw new Exception('La planta es obligatoria');
            }
            
            if (empty($nombre)) {
                throw new Exception('El nombre del botiquín es obligatorio');
            }
            
            // Crear el botiquín
            $data = [
                'id_planta' => $plantaId,
                'nombre' => $nombre,
                'activo' => true
            ];
            
            $this->botiquinService->createBotiquin($data);
            
            $this->session->setMessage('success', 'Botiquín creado correctamente');
            $this->redirectToIndex();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al crear botiquín: ' . $e->getMessage());
            $this->redirectToIndex();
        }
    }

    /**
     * Actualiza un botiquín existente
     */
    public function editar(): void
    {
        $this->authGuard->requireGestorPlanta();
        
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $plantaId = isset($_POST['planta_id']) ? (int)$_POST['planta_id'] : 0;
            $nombre = $_POST['nombre'] ?? '';
            
            // Validaciones
            if ($id <= 0) {
                throw new Exception('ID de botiquín inválido');
            }
            
            if ($plantaId <= 0) {
                throw new Exception('La planta es obligatoria');
            }
            
            if (empty($nombre)) {
                throw new Exception('El nombre del botiquín es obligatorio');
            }
            
            // Actualizar el botiquín
            $data = [
                'id_planta' => $plantaId,
                'nombre' => $nombre
            ];
            
            $this->botiquinService->updateBotiquin($id, $data);
            
            $this->session->setMessage('success', 'Botiquín actualizado correctamente');
            $this->redirectToIndex();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al actualizar botiquín: ' . $e->getMessage());
            $this->redirectToIndex();
        }
    }

    /**
     * Elimina un botiquín
     */
    public function eliminar(): void
    {
        $this->authGuard->requireGestorPlanta();
        
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            
            // Validaciones
            if ($id <= 0) {
                throw new Exception('ID de botiquín inválido');
            }
            
            // Comprobar si hay lecturas de stock asociadas
            // TODO: Implementar esta validación cuando exista el servicio de lecturas de stock
            
            // Eliminar el botiquín
            $eliminado = $this->botiquinService->deleteBotiquin($id);
            
            if ($eliminado) {
                $this->session->setMessage('success', 'Botiquín eliminado correctamente');
            } else {
                throw new Exception('No se pudo eliminar el botiquín');
            }
            
            $this->redirectToIndex();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al eliminar botiquín: ' . $e->getMessage());
            $this->redirectToIndex();
        }
    }
    
    /**
     * Desactiva un botiquín
     */
    public function desactivar(): void
    {
        $this->authGuard->requireGestorPlanta();
        
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            
            // Validaciones
            if ($id <= 0) {
                throw new Exception('ID de botiquín inválido');
            }
            
            // Desactivar el botiquín
            $desactivado = $this->botiquinService->desactivarBotiquin($id);
            
            if ($desactivado) {
                $this->session->setMessage('success', 'Botiquín desactivado correctamente');
            } else {
                throw new Exception('No se pudo desactivar el botiquín');
            }
            
            $this->redirectToIndex();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al desactivar botiquín: ' . $e->getMessage());
            $this->redirectToIndex();
        }
    }

    /**
     * Procesa solicitudes API para obtener datos
     */
    public function processApiRequest(): void
    {
        header('Content-Type: application/json');
        
        try {
            $action = $_GET['action'] ?? '';
            
            switch ($action) {
                case 'getById':
                    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                    $botiquin = $this->getById($id);
                    
                    if ($botiquin) {
                        echo json_encode([
                            'error' => false,
                            'botiquin' => $botiquin->toArray()
                        ]);
                    } else {
                        echo json_encode([
                            'error' => true,
                            'mensaje' => 'Botiquín no encontrado'
                        ]);
                    }
                    break;
                    
                case 'getByPlanta':
                    $plantaId = isset($_GET['plantaId']) ? (int)$_GET['plantaId'] : 0;
                    $botiquines = $this->getByPlanta($plantaId);
                    
                    echo json_encode([
                        'error' => false,
                        'botiquines' => array_map(function($b) { return $b->toArray(); }, $botiquines)
                    ]);
                    break;
                    
                case 'getByHospital':
                    $hospitalId = isset($_GET['hospitalId']) ? (int)$_GET['hospitalId'] : 0;
                    $botiquines = $this->getByHospital($hospitalId);
                    
                    echo json_encode([
                        'error' => false,
                        'botiquines' => array_map(function($b) { return $b->toArray(); }, $botiquines)
                    ]);
                    break;
                    
                default:
                    echo json_encode([
                        'error' => true,
                        'mensaje' => 'Acción no reconocida'
                    ]);
                    break;
            }
        } catch (Exception $e) {
            echo json_encode([
                'error' => true,
                'mensaje' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Procesa las solicitudes POST y GET
     */
    public function processRequest(): void
    {
        // Determinar si es una solicitud API
        $isApiRequest = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
                        
        if ($isApiRequest && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->processApiRequest();
            return;
        }
        
        // Si no es una petición POST, no hay nada que hacer
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
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
            case 'desactivar':
                $this->desactivar();
                break;
            default:
                $this->session->setMessage('error', 'Acción no reconocida');
                $this->redirectToIndex();
                break;
        }
    }
    
    /**
     * Redirecciona a la página de índice de botiquines
     */
    private function redirectToIndex(): void
    {
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/index.php');
        exit;
    }
}

// Ejecutar el controlador si este archivo es llamado directamente
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    $controller = new BotiquinController();
    $controller->processRequest();
}
