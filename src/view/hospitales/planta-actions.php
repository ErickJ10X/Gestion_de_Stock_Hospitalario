<?php
session_start();
require_once(__DIR__ . '/../../controller/PlantaController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\PlantaController;
use util\Session;
use util\AuthGuard;

$plantaController = new PlantaController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireHospitalGestor();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'crear_planta':
            crearPlanta();
            break;
        case 'editar_planta':
            editarPlanta();
            break;
        case 'eliminar_planta':
            eliminarPlanta();
            break;
        default:
            $session->setMessage('error', 'Acción de planta no reconocida');
            header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
            exit;
    }
} else {
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
