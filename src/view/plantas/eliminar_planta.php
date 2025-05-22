<?php
session_start();
require_once(__DIR__ . '/../../controller/PlantaController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

$plantaController = new \controller\PlantaController();
$session = new \util\Session();
$authGuard = new \util\AuthGuard();

$authGuard->requirePlantaGestor();

// Verificar que es una petición POST y que hay un ID
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id']) || empty($_POST['id'])) {
    $session->setMessage('error', 'Solicitud inválida');
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/listar_plantas.php');
    exit;
}

$id = $_POST['id'];

// Intentar eliminar la planta
if ($plantaController->deletePlanta($id)) {
    $session->setMessage('success', 'Planta eliminada correctamente');
} else {
    $session->setMessage('error', 'No se pudo eliminar la planta');
}

header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/listar_plantas.php');
exit;
