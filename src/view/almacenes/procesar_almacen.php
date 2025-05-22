<?php
session_start();
require_once __DIR__ . '/../../controller/AlmacenesController.php';
require_once __DIR__ . '/../../util/Session.php';
require_once __DIR__ . '/../../util/AuthGuard.php';
require_once __DIR__ . '/../../util/Redirect.php';

use controller\AlmacenesController;
use util\AuthGuard;

$authGuard = new AuthGuard();
$authGuard->checkSession();

$controller = new AlmacenesController();

// Procesa el formulario
$controller->processForm();

// Si llegamos aquí, algo falló en el procesamiento
Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/tabla_almacenes.php');
