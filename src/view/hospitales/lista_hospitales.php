<?php
session_start();
require_once(__DIR__ . '/../../controller/HospitalController.php');
require_once(__DIR__ . '/../../controller/PlantaController.php');
require_once(__DIR__ . '/../../controller/BotiquinController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\HospitalController;
use controller\PlantaController;
use controller\BotiquinController;
use util\Session;
use util\AuthGuard;

$hospitalController = new HospitalController();
$plantaController = new PlantaController();
$botiquinController = new BotiquinController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireHospitalGestor();

$hospitales = $hospitalController->index()['hospitales'] ?? [];
// Descomentar para cargar las plantas
$plantas = $plantaController->index()['plantas'] ?? [];
// Descomentar para cargar los botiquines
$botiquines = $botiquinController->index()['botiquines'] ?? [];

$pageTitle = "Hospitales";
include_once(__DIR__ . '/../templates/header.php');
?>

<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/card-form.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/tabs.css">

<div class="list-container">
    <div class="list-header">
        <div class="list-header__actions">
            <button id="btn-add-hospital" class="list-button list-button--success">
                <i class="bi bi-hospital"></i> Nuevo Hospital
            </button>
            <button id="btn-add-planta" class="list-button list-button--primary">
                <i class="bi bi-building"></i> Nueva Planta
            </button>
            <button id="btn-add-botiquin" class="list-button list-button--info">
                <i class="bi bi-box"></i> Nuevo Botiquín
            </button>
        </div>
    </div>

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

    <!-- Implementamos el sistema de pestañas -->
    <div class="tabs-container">
        <div class="tabs-nav">
            <button class="tab-btn" data-tab="tab-hospitales">Hospitales</button>
            <button class="tab-btn" data-tab="tab-plantas">Plantas</button>
            <button class="tab-btn active" data-tab="tab-botiquines">Botiquines</button>
        </div>

        <div class="tab-content">
            <!-- Pestaña Hospitales -->
            <div id="tab-hospitales" class="tab-pane">
                <?php include_once(__DIR__ . '/hospitales_tab.php'); ?>
            </div>

            <!-- Pestaña Plantas -->
            <div id="tab-plantas" class="tab-pane">
                <?php include_once(__DIR__ . '/plantas_tab.php'); ?>
            </div>

            <!-- Pestaña Botiquines -->
            <div id="tab-botiquines" class="tab-pane active">
                <?php include_once(__DIR__ . '/botiquines_tab.php'); ?>
            </div>
        </div>
    </div>
</div>

<!-- Overlay para ventanas modales -->
<div class="hospital-overlay"></div>

<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/hospital-cards.js"></script>
<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/tabs.js"></script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
