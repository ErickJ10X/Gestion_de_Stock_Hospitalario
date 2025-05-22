<?php
session_start();
require_once __DIR__ . '/../../controller/BotiquinesController.php';
require_once __DIR__ . '/../../util/Session.php';
require_once __DIR__ . '/../../util/AuthGuard.php';
require_once __DIR__ . '/../../util/Redirect.php';

use controller\BotiquinesController;
use util\AuthGuard;

$authGuard = new AuthGuard();
$authGuard->checkSession();

$controller = new BotiquinesController();

// Procesa el formulario
$controller->processForm();

// Si llegamos aquí, algo falló en el procesamiento
Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/tabla_botiquines.php');
