<?php
session_start();
require_once(__DIR__ . '/../../controller/PactosController.php');
require_once(__DIR__ . '/../../controller/ProductoController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\PactosController;
use controller\ProductoController;
use util\Session;
use util\AuthGuard;

$pactosController = new PactosController();
$productoController = new ProductoController();

$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireGestorHospital();

$pactos = $pactosController->index()['pactos'] ?? [];
$productos = $productoController->index()['productos'] ?? [];

$pageTitle = "Gestión de Pactos";
include_once(__DIR__ . '/../templates/header.php');
?>

<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/tabs.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/paginacion.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/pactos.css?v=<?= time() ?>">

<?php if ($session->hasMessage('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= $session->getMessage('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php $session->clearMessage('success'); ?>
<?php endif; ?>

<?php if ($session->hasMessage('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= $session->getMessage('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php $session->clearMessage('error'); ?>
<?php endif; ?>

<div class="tabs-container">
    <div class="tabs-nav">
        <button class="tab-btn active" data-tab="tab-pactos">Lista de Pactos</button>
        <button class="tab-btn" data-tab="tab-agregar-editar">Agregar/Editar</button>
    </div>

    <div class="tab-content">
        <div id="tab-pactos" class="tab-pane active">
            <?php include_once(__DIR__ . '/pactos_tab.php'); ?>
        </div>

        <div id="tab-agregar-editar" class="tab-pane">
            <?php include_once(__DIR__ . '/agregarEditar_tab.php'); ?>
        </div>
    </div>
</div>

<!-- Overlay para modales -->
<div class="hospital-overlay"></div>

<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/tabs.js"></script>
<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/pactos.js?v=<?= time() ?>"></script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
