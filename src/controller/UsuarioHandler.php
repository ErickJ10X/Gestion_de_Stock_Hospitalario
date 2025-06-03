<?php
namespace controller;

require_once(__DIR__ . '/../controller/UsuarioController.php');
require_once(__DIR__ . '/../util/Session.php');
require_once(__DIR__ . '/../util/AuthGuard.php');

use util\Session;
use util\AuthGuard;

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Crear controlador y verificar permisos
$authGuard = new AuthGuard();
$authGuard->requireAdministrador();

$usuarioController = new UsuarioController();

// Procesar la acción si existe
if (isset($_POST['action'])) {
    $usuarioController->handleAction($_POST['action']);
} else {
    // Redirigir a la lista si no hay acción especificada
    $session = new Session();
    $session->setMessage('error', 'Acción no especificada');
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/usuarios/');
    exit;
}
