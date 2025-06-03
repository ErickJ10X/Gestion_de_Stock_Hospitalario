<?php
session_start();
require_once(__DIR__ . '/../../controller/ReposicionesController.php');
require_once(__DIR__ . '/../../controller/ProductoController.php');
require_once(__DIR__ . '/../../controller/AlmacenesController.php');
require_once(__DIR__ . '/../../controller/BotiquinController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\ReposicionesController;
use controller\ProductoController;
use controller\AlmacenesController;
use controller\BotiquinController;
use util\Session;
use util\AuthGuard;

$reposicionesController = new ReposicionesController();
$productoController = new ProductoController();
$almacenesController = new AlmacenesController();
$botiquinController = new BotiquinController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireHospitalGestor();

$reposiciones = $reposicionesController->index()['reposiciones'] ?? [];
$productos = $productoController->index()['productos'] ?? [];
$almacenes = $almacenesController->index() ?? [];
$botiquines = $botiquinController->index()['botiquines'] ?? [];

$pageTitle = "Reposiciones";
include_once(__DIR__ . '/../templates/header.php');
?>

<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/card-form.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/tabs.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/reposiciones.css">

<!-- jQuery y DataTables primero para evitar problemas de carga -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

<!-- Bootstrap JS para modales -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

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
            <button class="tab-btn active" data-tab="tab-ver-estado">Ver Estado</button>
            <button class="tab-btn" data-tab="tab-generar-reposicion">Generar Reposición</button>
            <button class="tab-btn" data-tab="tab-marcar-entregado">Marcar como Entregado</button>
        </div>

        <div class="tab-content">
            <div id="tab-ver-estado" class="tab-pane active">
                <?php include_once(__DIR__ . '/ver_estado_tab.php'); ?>
            </div>
            <div id="tab-generar-reposicion" class="tab-pane">
                <?php include_once(__DIR__ . '/generar_reposicion_tab.php'); ?>
            </div>
            <div id="tab-marcar-entregado" class="tab-pane">
                <?php include_once(__DIR__ . '/marcar_entregado_tab.php'); ?>
            </div>
        </div>
    </div>
</div>

<div class="reposicion-overlay"></div>

<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/tabs.js"></script>
<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/reposiciones.js"></script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
