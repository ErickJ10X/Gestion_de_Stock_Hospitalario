<?php
session_start();
require_once(__DIR__ . '/../../controller/UsuarioController.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\UsuarioController;
use util\AuthGuard;

$usuarioController = new UsuarioController();
$authGuard = new AuthGuard();

$authGuard->requireAdministrador();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioController->handleAction();
} else {
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/usuarios/lista-usuarios.php');
    exit;
}
