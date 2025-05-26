<?php
session_start();
require_once __DIR__ . '/../../controller/BotiquinController.php';
require_once __DIR__ . '/../../util/Session.php';
require_once __DIR__ . '/../../util/AuthGuard.php';
require_once __DIR__ . '/../../util/Redirect.php';

use controller\BotiquinController;
use util\AuthGuard;

$authGuard = new AuthGuard();
$authGuard->requireAuth();

$controller = new BotiquinController();

$controller->processForm();

Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/tabla_botiquines.php');
