<?php
session_start();
require_once(__DIR__ . '/../../controller/AlmacenesController.php');
require_once(__DIR__ . '/../../controller/PlantaController.php');
require_once(__DIR__ . '/../../controller/ReposicionesController.php');
require_once(__DIR__ . '/../../controller/ProductoController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\AlmacenesController;
use controller\PlantaController;
use controller\ReposicionesController;
use controller\ProductoController;
use util\Session;
use util\AuthGuard;

$almacenesController = new AlmacenesController();
$plantaController = new PlantaController();
$reposicionesController = new ReposicionesController();
$productoController = new ProductoController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireHospitalGestor();

// Obtener datos necesarios para los informes
$almacenes = $almacenesController->index() ?? [];
$plantas = $plantaController->index()['plantas'] ?? [];
$reposiciones = $reposicionesController->index()['reposiciones'] ?? [];
$productos = $productoController->index()['productos'] ?? [];

$pageTitle = "Informes";
include_once(__DIR__ . '/../templates/header.php');
?>

<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/card-form.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/tabs.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/informes.css">

<!-- jQuery y DataTables primero para evitar problemas de carga -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

<!-- Bootstrap JS para modales -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js para gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>

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
            <button class="tab-btn active" data-tab="tab-actividad">Actividad Almacén y Planta</button>
            <button class="tab-btn" data-tab="tab-historico-reposiciones">Histórico Reposiciones</button>
        </div>

        <div class="tab-content">
            <div id="tab-actividad" class="tab-pane active">
                <?php include_once(__DIR__ . '/actividad_tab.php'); ?>
            </div>

            <div id="tab-historico-reposiciones" class="tab-pane">
                <?php include_once(__DIR__ . '/historico_reposiciones_tab.php'); ?>
            </div>
        </div>
    </div>
</div>

<div class="overlay"></div>

<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/tabs.js"></script>
<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/informes.js"></script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
