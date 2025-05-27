<?php
session_start();
require_once(__DIR__ . '/../../controller/HospitalController.php');
require_once(__DIR__ . '/../../controller/PlantaController.php');
require_once(__DIR__ . '/../../controller/BotiquinController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\HospitalController;
use controller\PlantaController;
use controller\BotiquinController;
use util\Session;
use util\AuthGuard;

$hospitalController = new HospitalController();
$plantaController = new PlantaController();
$botiquinController = new BotiquinController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireHospitalGestor();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'crear_hospital':
            crearHospital();
            break;
        case 'editar_hospital':
            editarHospital();
            break;
        case 'eliminar_hospital':
            eliminarHospital();
            break;
        case 'crear_planta':
            crearPlanta();
            break;
        case 'editar_planta':
            editarPlanta();
            break;
        case 'eliminar_planta':
            eliminarPlanta();
            break;
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
            $session->setMessage('error', 'Acción no reconocida');
            header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
            exit;
    }
} else {
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
    exit;
}

function crearHospital() {
    global $hospitalController, $session;
    
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    
    if (empty($nombre)) {
        $session->setMessage('modal_error_hospital', 'El nombre del hospital es obligatorio');
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
        exit;
    }
    
    $response = $hospitalController->store($nombre);
    
    if ($response['error']) {
        $session->setMessage('modal_error_hospital', $response['mensaje']);
    } else {
        $session->setMessage('success', $response['mensaje']);
    }
    
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
    exit;
}

function editarHospital() {
    global $hospitalController, $session;
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    
    if ($id <= 0 || empty($nombre)) {
        $session->setMessage('modal_error_hospital_' . $id, 'El ID y el nombre del hospital son obligatorios');
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
        exit;
    }
    
    $response = $hospitalController->update($id, $nombre);
    
    if ($response['error']) {
        $session->setMessage('modal_error_hospital_' . $id, $response['mensaje']);
    } else {
        $session->setMessage('success', $response['mensaje']);
    }
    
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
    exit;
}

function eliminarHospital() {
    global $hospitalController, $session;
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if ($id <= 0) {
        $session->setMessage('error', 'ID de hospital inválido');
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
        exit;
    }
    
    $response = $hospitalController->destroy($id);
    
    if ($response['error']) {
        $session->setMessage('error', $response['mensaje']);
    } else {
        $session->setMessage('success', $response['mensaje']);
    }
    
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
    exit;
}

function crearPlanta() {
    global $plantaController, $session;
    
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $idHospital = isset($_POST['id_hospital']) ? (int)$_POST['id_hospital'] : 0;
    
    if (empty($nombre) || $idHospital <= 0) {
        $session->setMessage('modal_error_planta', 'El nombre de la planta y el hospital son obligatorios');
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
        exit;
    }
    
    $response = $plantaController->store($nombre, $idHospital);
    
    if ($response['error']) {
        $session->setMessage('modal_error_planta', $response['mensaje']);
    } else {
        $session->setMessage('success', $response['mensaje']);
    }
    
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
    exit;
}

function editarPlanta() {
    global $plantaController, $session;
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $idHospital = isset($_POST['id_hospital']) ? (int)$_POST['id_hospital'] : 0;
    
    if ($id <= 0 || empty($nombre) || $idHospital <= 0) {
        $session->setMessage('modal_error_planta_' . $id, 'El ID, nombre de la planta y hospital son obligatorios');
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
        exit;
    }
    
    $response = $plantaController->update($id, $nombre, $idHospital);
    
    if ($response['error']) {
        $session->setMessage('modal_error_planta_' . $id, $response['mensaje']);
    } else {
        $session->setMessage('success', $response['mensaje']);
    }
    
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
    exit;
}

function eliminarPlanta() {
    global $plantaController, $session;
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if ($id <= 0) {
        $session->setMessage('error', 'ID de planta inválido');
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
        exit;
    }
    
    $response = $plantaController->destroy($id);
    
    if ($response['error']) {
        $session->setMessage('error', $response['mensaje']);
    } else {
        $session->setMessage('success', $response['mensaje']);
    }
    
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
