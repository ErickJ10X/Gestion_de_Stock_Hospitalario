<?php
session_start();
require_once(__DIR__ . '/../../controller/PlantaController.php');
require_once(__DIR__ . '/../../util/Session.php');
require_once(__DIR__ . '/../../util/AuthGuard.php');
require_once(__DIR__ . '/../../util/Redirect.php');

use controller\PlantaController;
use util\Session;
use util\AuthGuard;
use util\Redirect;
use Exception;

$plantaController = new PlantaController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requirePlantaGestor();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action'])) {
    $session->setMessage('error', 'Solicitud inválida');
    Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/listar_plantas.php');
    exit;
}

$action = $_POST['action'];

try {
    if ($action === 'create') {
        if (!isset($_POST['nombre']) || !isset($_POST['hospital_id'])) {
            $session->setMessage('error', 'Faltan datos obligatorios');
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/listar_plantas.php');
            exit;
        }
        
        $nombre = trim($_POST['nombre']);
        $hospitalId = $_POST['hospital_id'];
        
        if (empty($nombre) || empty($hospitalId)) {
            $session->setMessage('error', 'El nombre y el hospital son obligatorios');
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/listar_plantas.php');
            exit;
        }
        
        if ($plantaController->createPlanta($nombre, $hospitalId)) {
            $session->setMessage('success', 'Planta creada correctamente');
        } else {
            $session->setMessage('error', 'Error al crear la planta');
        }
    }
    elseif ($action === 'update') {
        if (!isset($_POST['id']) || !isset($_POST['nombre']) || !isset($_POST['hospital_id'])) {
            $session->setMessage('error', 'Faltan datos obligatorios');
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/listar_plantas.php');
            exit;
        }
        
        $id = $_POST['id'];
        $nombre = trim($_POST['nombre']);
        $hospitalId = $_POST['hospital_id'];
        
        if (empty($id) || empty($nombre) || empty($hospitalId)) {
            $session->setMessage('error', 'Todos los campos son obligatorios');
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/listar_plantas.php');
            exit;
        }
        
        if ($plantaController->updatePlanta($id, $nombre, $hospitalId)) {
            $session->setMessage('success', 'Planta actualizada correctamente');
        } else {
            $session->setMessage('error', 'Error al actualizar la planta');
        }
    }
    elseif ($action === 'delete') {
        if (!isset($_POST['id'])) {
            $session->setMessage('error', 'ID de planta no proporcionado');
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/listar_plantas.php');
            exit;
        }
        
        $id = $_POST['id'];
        
        if (empty($id)) {
            $session->setMessage('error', 'ID de planta no válido');
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/listar_plantas.php');
            exit;
        }
        
        if ($plantaController->deletePlanta($id)) {
            $session->setMessage('success', 'Planta eliminada correctamente');
        } else {
            $session->setMessage('error', 'Error al eliminar la planta. Puede que tenga botiquines o almacenes asociados.');
        }
    }
    else {
        $session->setMessage('error', 'Acción no reconocida');
    }
} catch (Exception $e) {
    $session->setMessage('error', 'Error: ' . $e->getMessage());
}

Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/listar_plantas.php');
exit;
