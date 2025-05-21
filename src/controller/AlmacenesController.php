<?php

namespace controller;

use Exception;
use model\service\AlmacenesService;
use model\service\PlantaService;
use util\AuthGuard;
use util\Redirect;
use util\Session;

require_once(__DIR__ . '/../model/service/AlmacenesService.php');
require_once(__DIR__ . '/../model/service/PlantaService.php');
require_once(__DIR__ . '/../util/AuthGuard.php');
require_once(__DIR__ . '/../util/Redirect.php');
require_once(__DIR__ . '/../util/Session.php');

class AlmacenesController
{
    private $almacenesService;
    private $plantaService;
    private $session;
    private $authGuard;

    public function __construct()
    {
        $this->almacenesService = new AlmacenesService();
        $this->plantaService = new PlantaService();
        $this->session = new Session();
        $this->authGuard = new AuthGuard();
    }

    public function getAllAlmacenes(): array
    {
        $this->authGuard->requireAuth();
        try {
            return $this->almacenesService->getAllAlmacenes();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al obtener almacenes: ' . $e->getMessage());
            return [];
        }
    }

    public function getAlmacenesWithPlantas()
    {
        $this->authGuard->requireAuth();
        try {
            $almacenes = $this->almacenesService->getAllAlmacenes();
            $result = [];
            
            foreach ($almacenes as $almacen) {
                $planta = $this->plantaService->getPlantaById($almacen->getPlantaId());
                $result[] = [
                    'almacen' => $almacen,
                    'planta' => $planta
                ];
            }
            
            return $result;
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al obtener almacenes: ' . $e->getMessage());
            return [];
        }
    }

    public function getPlantas()
    {
        $this->authGuard->requireAuth();
        try {
            return $this->plantaService->getAllPlantas();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al obtener plantas: ' . $e->getMessage());
            return [];
        }
    }

    public function getAlmacenById($id)
    {
        $this->authGuard->requireAuth();
        try {
            return $this->almacenesService->getAlmacenById($id);
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al obtener almacén: ' . $e->getMessage());
            return null;
        }
    }

    public function processForm()
    {
        $this->authGuard->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'create':
                    return $this->handleCreate();
                case 'update':
                    return $this->handleUpdate();
                case 'delete':
                    return $this->handleDelete();
                default:
                    $this->session->setMessage('error', 'Acción desconocida');
                    Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/tabla_almacenes.php');
            }
        }
    }
    
    private function handleCreate()
    {
        try {
            $planta_id = $_POST['planta_id'] ?? '';
            
            if (empty($planta_id)) {
                $this->session->setMessage('error', 'El campo planta es obligatorio');
                return Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/crear_almacen.php');
            }
            
            $result = $this->almacenesService->createAlmacen($planta_id);
            
            if ($result) {
                $this->session->setMessage('success', 'Almacén creado correctamente');
                Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/tabla_almacenes.php');
            } else {
                $this->session->setMessage('error', 'Error al crear el almacén');
                Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/crear_almacen.php');
            }
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error: ' . $e->getMessage());
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/crear_almacen.php');
        }
    }
    
    private function handleUpdate()
    {
        try {
            $id = $_POST['id'] ?? '';
            $planta_id = $_POST['planta_id'] ?? '';
            
            if (empty($id) || empty($planta_id)) {
                $this->session->setMessage('error', 'Todos los campos son obligatorios');
                return Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/editar_almacen.php?id=' . $id);
            }
            
            $result = $this->almacenesService->updateAlmacen($id, $planta_id);
            
            if ($result) {
                $this->session->setMessage('success', 'Almacén actualizado correctamente');
                Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/tabla_almacenes.php');
            } else {
                $this->session->setMessage('error', 'Error al actualizar el almacén');
                Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/editar_almacen.php?id=' . $id);
            }
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error: ' . $e->getMessage());
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/tabla_almacenes.php');
        }
    }
    
    private function handleDelete()
    {
        try {
            $id = $_POST['id'] ?? '';
            
            if (empty($id)) {
                $this->session->setMessage('error', 'ID de almacén no proporcionado');
                return Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/tabla_almacenes.php');
            }
            
            $result = $this->almacenesService->deleteAlmacen($id);
            
            if ($result) {
                $this->session->setMessage('success', 'Almacén eliminado correctamente');
            } else {
                $this->session->setMessage('error', 'Error al eliminar el almacén');
            }
            
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/tabla_almacenes.php');
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error: ' . $e->getMessage());
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/tabla_almacenes.php');
        }
    }
}
