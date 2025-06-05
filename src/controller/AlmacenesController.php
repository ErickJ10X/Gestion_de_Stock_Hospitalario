<?php

namespace controller;

use Exception;
use model\entity\Almacen;
use model\service\AlmacenService;
use model\repository\AlmacenRepository;
use util\Session;
use util\AuthGuard;

require_once(__DIR__ . '/../model/service/AlmacenService.php');
require_once(__DIR__ . '/../model/repository/AlmacenRepository.php');
require_once(__DIR__ . '/../model/entity/Almacen.php');
require_once(__DIR__ . '/../util/Session.php');
require_once(__DIR__ . '/../util/AuthGuard.php');

class AlmacenesController
{
    private AlmacenService $almacenService;
    private Session $session;
    private AuthGuard $authGuard;

    public function __construct()
    {
        $this->almacenService = new AlmacenService(new AlmacenRepository());
        $this->session = new Session();
        $this->authGuard = new AuthGuard();
    }

    /**
     * Método principal para obtener los almacenes
     * @return array Lista de almacenes
     */
    public function index(): array
    {
        try {
            return $this->almacenService->getAllAlmacenes();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al cargar los almacenes: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene almacenes por planta
     * @param int $plantaId ID de la planta
     * @return array Lista de almacenes de una planta específica
     */
    public function getByPlanta(int $plantaId): array
    {
        try {
            return $this->almacenService->getAlmacenesByPlanta($plantaId);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtiene almacenes por hospital
     * @param int $hospitalId ID del hospital
     * @return array Lista de almacenes de un hospital específico
     */
    public function getByHospital(int $hospitalId): array
    {
        try {
            return $this->almacenService->getAlmacenesByHospital($hospitalId);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtiene un almacén por su ID
     * @param int $id ID del almacén
     * @return Almacen|null El almacén encontrado o null
     */
    public function getById(int $id): ?Almacen
    {
        try {
            return $this->almacenService->getAlmacenById($id);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Crea un nuevo almacén
     */
    public function crear(): void
    {
        $this->authGuard->requireGestorGeneral();
        
        try {
            $tipo = $_POST['tipo'] ?? '';
            $plantaId = isset($_POST['planta_id']) ? (int)$_POST['planta_id'] : null;
            $hospitalId = isset($_POST['hospital_id']) ? (int)$_POST['hospital_id'] : 0;
            
            // Validaciones
            if (empty($tipo)) {
                throw new Exception('El tipo de almacén es obligatorio');
            }
            
            if ($hospitalId <= 0) {
                throw new Exception('El hospital es obligatorio');
            }
            
            // Crear el almacén
            $data = [
                'tipo' => $tipo,
                'id_planta' => $plantaId,
                'id_hospital' => $hospitalId,
                'activo' => true
            ];
            
            $this->almacenService->createAlmacen($data);
            
            $this->session->setMessage('success', 'Almacén creado correctamente');
            $this->redirectToIndex();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al crear almacén: ' . $e->getMessage());
            $this->redirectToIndex();
        }
    }

    /**
     * Actualiza un almacén existente
     */
    public function editar(): void
    {
        $this->authGuard->requireGestorGeneral();
        
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $tipo = $_POST['tipo'] ?? '';
            $plantaId = isset($_POST['planta_id']) ? (int)$_POST['planta_id'] : null;
            $hospitalId = isset($_POST['hospital_id']) ? (int)$_POST['hospital_id'] : 0;
            
            // Validaciones
            if ($id <= 0) {
                throw new Exception('ID de almacén inválido');
            }
            
            if (empty($tipo)) {
                throw new Exception('El tipo de almacén es obligatorio');
            }
            
            if ($hospitalId <= 0) {
                throw new Exception('El hospital es obligatorio');
            }
            
            // Actualizar el almacén
            $data = [
                'tipo' => $tipo,
                'id_planta' => $plantaId,
                'id_hospital' => $hospitalId
            ];
            
            $this->almacenService->updateAlmacen($id, $data);
            
            $this->session->setMessage('success', 'Almacén actualizado correctamente');
            $this->redirectToIndex();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al actualizar almacén: ' . $e->getMessage());
            $this->redirectToIndex();
        }
    }

    /**
     * Elimina un almacén
     */
    public function eliminar(): void
    {
        $this->authGuard->requireGestorGeneral  ();
        
        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            
            // Validaciones
            if ($id <= 0) {
                throw new Exception('ID de almacén inválido');
            }
            
            // Eliminar el almacén
            $eliminado = $this->almacenService->deleteAlmacen($id);
            
            if ($eliminado) {
                $this->session->setMessage('success', 'Almacén eliminado correctamente');
            } else {
                throw new Exception('No se pudo eliminar el almacén');
            }
            
            $this->redirectToIndex();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al eliminar almacén: ' . $e->getMessage());
            $this->redirectToIndex();
        }
    }
    
    /**
     * Procesa las solicitudes POST
     */
    public function processRequest(): void
    {
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
            default:
                $this->session->setMessage('error', 'Acción no reconocida');
                $this->redirectToIndex();
                break;
        }
    }
    
    /**
     * Redirecciona a la página de índice de almacenes
     */
    private function redirectToIndex(): void
    {
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/index.php');
        exit;
    }
}

// Ejecutar el controlador si este archivo es llamado directamente
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    $controller = new AlmacenesController();
    $controller->processRequest();
}
