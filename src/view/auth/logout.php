<?php

use controller\AuthController;
use util\AuthGuard;

session_start();
require_once(__DIR__ . '/../../controller/AuthController.php');
require_once(__DIR__ . '/../../util/AuthGuard.php');

// Agregar AuthGuard para asegurar que el usuario esté autenticado antes de cerrar sesión
$authGuard = new AuthGuard();
$authGuard->requireAuth();

$authController = new AuthController();
$authController->logout();
