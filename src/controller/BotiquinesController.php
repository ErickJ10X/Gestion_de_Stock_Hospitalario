<?php

namespace controller;

use Exception;
use model\service\BotiquinesService;
use model\service\PlantaService;
use util\AuthGuard;
use util\Redirect;
use util\Session;

require_once(__DIR__ . '/../model/service/BotiquinesService.php');
require_once(__DIR__ . '/../model/service/PlantaService.php');
require_once(__DIR__ . '/../util/AuthGuard.php');
require_once(__DIR__ . '/../util/Redirect.php');
require_once(__DIR__ . '/../util/Session.php');

class BotiquinesController
{
    private $botiquinesService;
    private $plantaService;
    private $session;
    private $authGuard;

    public function __construct()
    {
        $this->botiquinesService = new BotiquinesService();
        $this->plantaService = new PlantaService();
        $this->session = new Session();
        $this->authGuard = new AuthGuard();
    }

    public function getBotiquines()
    {
        $this->authGuard->requireAuth();
        try {
            return $this->botiquinesService->getAllBotiquines();
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al obtener botiquines: ' . $e->getMessage());
            return [];
        }
    }

    public function getBotiquinesWithPlantas()
    {
        $this->authGuard->requireAuth();
        try {
            $botiquines = $this->botiquinesService->getAllBotiquines();
            $result = [];
            
            foreach ($botiquines as $botiquin) {
                $planta = $this->plantaService->getPlantaById($botiquin->getPlantaId());
                $result[] = [
                    'botiquin' => $botiquin,
                    'planta' => $planta
                ];
            }
            
            return $result;
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al obtener botiquines: ' . $e->getMessage());
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

    public function getBotiquinById($id)
    {
        $this->authGuard->requireNoAuth();
        try {
            return $this->botiquinesService->getBotiquinById($id);
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error al obtener botiquín: ' . $e->getMessage());
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
                    Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/tabla_botiquines.php');
            }
        }
    }
    
    private function handleCreate()
    {
        try {
            $nombre = $_POST['nombre'] ?? '';
            $plantaId = $_POST['planta_id'] ?? '';
            
            if (empty($nombre) || empty($plantaId)) {
                $this->session->setMessage('error', 'Todos los campos son obligatorios');
                return Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/crear_botiquin.php');
            }
            
            $result = $this->botiquinesService->createBotiquin($nombre, $plantaId);
            
            if ($result) {
                $this->session->setMessage('success', 'Botiquín creado correctamente');
                Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/tabla_botiquines.php');
            } else {
                $this->session->setMessage('error', 'Error al crear el botiquín');
                Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/crear_botiquin.php');
            }
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error: ' . $e->getMessage());
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/crear_botiquin.php');
        }
    }
    
    private function handleUpdate()
    {
        try {
            $id = $_POST['id'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $plantaId = $_POST['planta_id'] ?? '';
            
            if (empty($id) || empty($nombre) || empty($plantaId)) {
                $this->session->setMessage('error', 'Todos los campos son obligatorios');
                return Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/editar_botiquin.php?id=' . $id);
            }
            
            $result = $this->botiquinesService->updateBotiquin($id, $nombre, $plantaId);
            
            if ($result) {
                $this->session->setMessage('success', 'Botiquín actualizado correctamente');
                Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/tabla_botiquines.php');
            } else {
                $this->session->setMessage('error', 'Error al actualizar el botiquín');
                Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/editar_botiquin.php?id=' . $id);
            }
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error: ' . $e->getMessage());
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/tabla_botiquines.php');
        }
    }
    
    private function handleDelete()
    {
        try {
            $id = $_POST['id'] ?? '';
            
            if (empty($id)) {
                $this->session->setMessage('error', 'ID de botiquín no proporcionado');
                return Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/tabla_botiquines.php');
            }
            
            $result = $this->botiquinesService->deleteBotiquin($id);
            
            if ($result) {
                $this->session->setMessage('success', 'Botiquín eliminado correctamente');
            } else {
                $this->session->setMessage('error', 'Error al eliminar el botiquín');
            }
            
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/tabla_botiquines.php');
        } catch (Exception $e) {
            $this->session->setMessage('error', 'Error: ' . $e->getMessage());
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/tabla_botiquines.php');
        }
    }
}
