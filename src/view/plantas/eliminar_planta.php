<?php
session_start();
require_once(__DIR__ . '/../../controller/PlantaController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

$plantaController = new \controller\PlantaController();
$session = new \util\Session();
$authGuard = new \util\AuthGuard();

$authGuard->requirePlantaGestor();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id']) || empty($_POST['id'])) {
    $session->setMessage('error', 'Solicitud inválida');
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/listar_plantas.php');
    exit;
}

$id = $_POST['id'];

if ($plantaController->deletePlanta($id)) {
    $session->setMessage('success', 'Plantas eliminada correctamente');
} else {
    $session->setMessage('error', 'No se pudo eliminar la planta');
}

header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/listar_plantas.php');
exit;
