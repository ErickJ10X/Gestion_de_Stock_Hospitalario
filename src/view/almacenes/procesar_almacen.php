<?php
session_start();
require_once __DIR__ . '/../../controller/AlmacenesController.php';
require_once __DIR__ . '/../../util/Session.php';
require_once __DIR__ . '/../../util/AuthGuard.php';
require_once __DIR__ . '/../../util/Redirect.php';

use controller\AlmacenesController;
use util\AuthGuard;

$authGuard = new AuthGuard();
$authGuard->requireAuth();

$controller = new AlmacenesController();

$controller->processForm();

Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/tabla_almacenes.php');
