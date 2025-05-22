<?php
session_start();
require_once(__DIR__ . '/../../controller/ProductosController.php');
require_once(__DIR__ . '/../../util/Session.php');
require_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\ProductosController;
use util\Session;
use util\AuthGuard;

$authGuard = new AuthGuard();
$authGuard->requireAuth();

$productosController = new ProductosController();
$productos = $productosController->getAllProductos();

$session = new Session();

$pageTitle = "Gestión de Productos";
include_once(__DIR__ . '/../templates/header.php');
?>

<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/card-form.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css">

<div class="list-container">
    <div class="list-header">
        <h2 class="list-header__title">Gestión de Productos</h2>
        <div class="list-header__actions">
            <button id="btn-add-producto" class="list-button list-button--primary">
                <i class="bi bi-plus-circle list-button__icon"></i> Nuevo Producto
            </button>
        </div>
    </div>

    <?php if ($session->hasMessage('success')): ?>
        <div class="list-alert list-alert--success">
            <p class="list-alert