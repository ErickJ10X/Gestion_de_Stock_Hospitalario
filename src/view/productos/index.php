<?php
session_start();
require_once(__DIR__ . '/../../controller/ProductoController.php');
require_once(__DIR__ . '/../../controller/CatalogosController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\CatalogosController;
use controller\ProductoController;
use util\Session;
use util\AuthGuard;

$productoController = new ProductoController();
$catalogosController = new CatalogosController();

$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireGestorHospital();

$productos = $productoController->index()['productos'] ?? [];
$catalogos = $catalogosController->index()['catalogos'] ?? [];

$pageTitle = "Gestión de Productos";
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
            <button class="tab-btn active" data-tab="tab-productos">Catálogo de Productos</button>
            <button class="tab-btn" data-tab="tab-agregar-editar">Agregar/Editar</button>
        </div>

        <div class="tab-content">
            <div id="tab-productos" class="tab-pane active">
                <?php include_once(__DIR__ . '/productos_tab.php'); ?>
            </div>
            
            <div id="tab-agregar-editar" class="tab-pane">
                <?php include_once(__DIR__ . '/agregarEditar_tab.php'); ?>
            </div>
        </div>
    </div>
</div>

<div class="hospital-overlay"></div>

<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/productos.js"></script>
<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/tabs.js"></script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
