<?php
session_start();
require_once(__DIR__ . '/../../controller/AlmacenesController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\AlmacenesController;
use util\Session;
use util\AuthGuard;

$almacenesController = new AlmacenesController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireHospitalGestor();

$almacenes = $almacenesController->index()['almacenes'] ?? [];

$pageTitle = "Almacenes";
include_once(__DIR__ . '/../templates/header.php');
?>

<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/card-form.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/tabs.css">

<div class="list-container">
    <div class="list-header">
        <div class="list-header__actions">
            <button id="btn-add-almacen" class="list-button list-button--success">
                <i class="bi bi-box-seam"></i> Nuevo Almacén
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
            <button class="tab-btn active" data-tab="tab-almacenes">Almacenes</button>
        </div>

        <div class="tab-content">
            <!-- Pestaña Almacenes -->
            <div id="tab-almacenes" class="tab-pane active">
                <?php include_once(__DIR__ . '/almacenes_tab.php'); ?>
            </div>
        </div>
    </div>
</div>

<!-- Overlay para ventanas modales -->
<div class="almacen-overlay"></div>

<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/tabs.js"></script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
