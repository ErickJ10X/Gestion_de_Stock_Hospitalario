<?php
session_start();
require_once(__DIR__ . '/../../controller/HospitalController.php');
include_once(__DIR__ . '/../../util/session.php');
include_once(__DIR__ . '/../../util/authGuard.php');

$hospitalController = new HospitalController();
$session = new Session();
$authGuard = new AuthGuard();

// Verificar permisos usando AuthGuard
$authGuard->requireHospitalGestor();

// Procesar el formulario si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    $nombre = trim($_POST['nombre']);
    
    if (empty($nombre)) {
        $session->setMessage('error', 'El nombre del hospital es obligatorio');
    } else {
        if ($hospitalController->createHospital($nombre)) {
            $session->setMessage('success', 'Hospital registrado correctamente');
            header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/listar_hospitales.php');
            exit;
        }
    }
}

$pageTitle = "Registrar Hospital";
include_once(__DIR__ . '/../templates/header.php');
?>

<!-- Resto del código HTML del formulario -->
