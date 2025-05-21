<?php
session_start();
require_once(__DIR__ . '/../../controller/PlantaController.php');
require_once(__DIR__ . '/../../controller/HospitalController.php');
include_once(__DIR__ . '/../../util/session.php');
include_once(__DIR__ . '/../../util/authGuard.php');

$plantaController = new PlantaController();
$hospitalController = new HospitalController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requirePlantaGestor();

try {
    $hospitales = $hospitalController->getAllHospitales();
} catch (Exception $e) {
    $hospitales = [];
    $session->setMessage('error', 'Error al cargar los hospitales: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre']) && isset($_POST['hospital_id'])) {
    $nombre = trim($_POST['nombre']);
    $hospitalId = $_POST['hospital_id'];
    
    if (empty($nombre)) {
        $session->setMessage('error', 'El nombre de la planta es obligatorio');
    } elseif (empty($hospitalId)) {
        $session->setMessage('error', 'Debe seleccionar un hospital');
    } else {
        if ($plantaController->createPlanta($nombre, $hospitalId)) {
            $session->setMessage('success', 'Planta registrada correctamente');
            header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/listar_plantas.php');
            exit;
        }
    }
}

$pageTitle = "Registrar Planta";
include_once(__DIR__ . '/../templates/header.php');
?>
