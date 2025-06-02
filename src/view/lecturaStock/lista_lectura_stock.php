<?php
session_start();
require_once(__DIR__ . '/../../controller/LecturasStockController.php');
require_once(__DIR__ . '/../../controller/BotiquinController.php');
require_once(__DIR__ . '/../../controller/ProductoController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\LecturasStockController;
use controller\BotiquinController;
use controller\ProductoController;
use util\Session;
use util\AuthGuard;

$lecturasStockController = new LecturasStockController();
$botiquinController = new BotiquinController();
$productoController = new ProductoController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireHospitalGestor();

$botiquines = $botiquinController->index()['botiquines'] ?? [];
$resultadoLecturas = $lecturasStockController->index();
$lecturas = $resultadoLecturas['error'] ? [] : $resultadoLecturas['lecturas'];

$pageTitle = "Lecturas de Stock";
include_once(__DIR__ . '/../templates/header.php');
?>

<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/card-form.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/tabs.css">

<div class="list-container">

    <?php if ($session->hasMessage('success')): ?>
        <div class="list-alert list-alert--success">
            <p class="list-alert__message"><?= $session->getMessage('success') ?></p>
            <button type="button" class="list-alert__close">&times;</button>
        </div>
        <?php $session->clearMessage('success'); ?>
    <?php endif; ?>

    <?php if ($session->hasMessage('error')): ?>
        <div class="list-alert list-alert--error">
            <p class="list-alert__message"><?= $session->getMessage('error') ?></p>
            <button type="button" class="list-alert__close">&times;</button>
        </div>
        <?php $session->clearMessage('error'); ?>
    <?php endif; ?>

    <div class="tabs-container">
        <div class="tabs-nav">
            <button class="tab-btn active" data-tab="tab-registrar-lectura">Registrar Lectura</button>
            <button class="tab-btn" data-tab="tab-historico-lecturas">Histórico de Lecturas</button>
        </div>

        <div class="tab-content">
            <div id="tab-registrar-lectura" class="tab-pane active">
                <?php include_once(__DIR__ . '/registrar_lectura_tab.php'); ?>
            </div>
            <div id="tab-historico-lecturas" class="tab-pane">
                <?php include_once(__DIR__ . '/historico_lecturas_tab.php'); ?>
            </div>
        </div>
    </div>
</div>

<div class="hospital-overlay"></div>

<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/tabs.js"></script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
