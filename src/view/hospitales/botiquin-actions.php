<?php
session_start();
require_once(__DIR__ . '/../../controller/BotiquinController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\BotiquinController;
use util\Session;
use util\AuthGuard;

$botiquinController = new BotiquinController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireHospitalGestor();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'crear_botiquin':
            crearBotiquin();
            break;
        case 'editar_botiquin':
            editarBotiquin();
            break;
        case 'eliminar_botiquin':
            eliminarBotiquin();
            break;
        default:
            $session->setMessage('error', 'Acción de botiquín no reconocida');
            header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
            exit;
    }
} else {
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
    exit;
}

function crearBotiquin() {
    global $botiquinController, $session;
    
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $plantaId = isset($_POST['planta_id']) ? (int)$_POST['planta_id'] : 0;
    
    if (empty($nombre) || $plantaId <= 0) {
        $session->setMessage('modal_error_botiquin', 'El nombre del botiquín y la planta son obligatorios');
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
        exit;
    }
    
    $response = $botiquinController->store($nombre, $plantaId);
    
    if ($response['error']) {
        $session->setMessage('modal_error_botiquin', $response['mensaje']);
    } else {
        $session->setMessage('success', $response['mensaje']);
    }
    
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
    exit;
}

function editarBotiquin() {
    global $botiquinController, $session;
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $plantaId = isset($_POST['planta_id']) ? (int)$_POST['planta_id'] : 0;
    
    if ($id <= 0 || empty($nombre) || $plantaId <= 0) {
        $session->setMessage('modal_error_botiquin_' . $id, 'El ID, nombre del botiquín y la planta son obligatorios');
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
        exit;
    }
    
    $response = $botiquinController->update($id, $nombre, $plantaId);
    
    if ($response['error']) {
        $session->setMessage('modal_error_botiquin_' . $id, $response['mensaje']);
    } else {
        $session->setMessage('success', $response['mensaje']);
    }
    
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
    exit;
}

function eliminarBotiquin() {
    global $botiquinController, $session;
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if ($id <= 0) {
        $session->setMessage('error', 'ID de botiquín inválido');
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
        exit;
    }
    
    $response = $botiquinController->destroy($id);
    
    if ($response['error']) {
        $session->setMessage('error', $response['mensaje']);
    } else {
        $session->setMessage('success', $response['mensaje']);
    }
    
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
    exit;
}
