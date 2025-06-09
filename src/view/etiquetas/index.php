<?php
session_start();
require_once(__DIR__ . '/../../controller/EtiquetasController.php');
require_once(__DIR__ . '/../../controller/ProductoController.php');
require_once(__DIR__ . '/../../controller/ReposicionesController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\EtiquetasController;
use controller\ProductoController;
use controller\ReposicionesController;
use util\Session;
use util\AuthGuard;

$etiquetasController = new EtiquetasController();
$productoController = new ProductoController();
$reposicionController = new ReposicionesController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireGestorHospital();

// Manejo de acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion']) && isset($_POST['id_etiqueta'])) {
        $idEtiqueta = $_POST['id_etiqueta'];
        
        if ($_POST['accion'] === 'imprimir') {
            $resultado = $etiquetasController->marcarComoImpresa($idEtiqueta);
            if (!$resultado['error']) {
                $session->setMessage('success', $resultado['mensaje']);
            } else {
                $session->setMessage('error', $resultado['mensaje']);
            }
        } elseif ($_POST['accion'] === 'eliminar') {
            $resultado = $etiquetasController->destroy($idEtiqueta);
            if (!$resultado['error']) {
                $session->setMessage('success', $resultado['mensaje']);
            } else {
                $session->setMessage('error', $resultado['mensaje']);
            }
        }
    }
}

// Obtener etiquetas
$resultado = $etiquetasController->index();
$etiquetas = !isset($resultado['error']) || !$resultado['error'] && isset($resultado['etiquetas']) ? $resultado['etiquetas'] : [];

// Obtener información de los productos para mostrar en la tabla
$productosInfo = [];
if (!empty($etiquetas)) {
    foreach ($etiquetas as $etiqueta) {
        $idProducto = $etiqueta->getIdProducto();
        if (!isset($productosInfo[$idProducto])) {
            $resultadoProducto = $productoController->getById($idProducto);
            if (!isset($resultadoProducto['error']) || !$resultadoProducto['error'] && isset($resultadoProducto['producto'])) {
                // Almacenar el objeto Producto completo
                $productosInfo[$idProducto] = $resultadoProducto['producto'];
            }
        }
    }
}

// Obtener productos y reposiciones para el formulario de generación
$resultadoProductos = $productoController->index();
$resultadoReposiciones = $reposicionController->index();

// Variables para el formulario de generar etiquetas
$productos = [];
if (isset($resultadoProductos['productos'])) {
    $productos = $resultadoProductos['productos'];
}

$reposiciones = [];
if (isset($resultadoReposiciones['reposiciones'])) {
    $reposiciones = $resultadoReposiciones['reposiciones'];
}

$pageTitle = "Gestión de Etiquetas";
include_once(__DIR__ . '/../templates/header.php');
?>

<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/tabs.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/paginacion.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/etiquetas.css">

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
        <button class="tab-btn active" data-tab="tab-etiquetas">Etiquetas</button>
        <button class="tab-btn" data-tab="tab-generar">Generar Etiqueta</button>
    </div>

    <div class="tab-content">
        <div id="tab-etiquetas" class="tab-pane active">
            <?php include_once(__DIR__ . '/etiquetas_tab.php'); ?>
        </div>

        <div id="tab-generar" class="tab-pane">
            <?php include_once(__DIR__ . '/generar_tab.php'); ?>
        </div>
    </div>
</div>

<div class="etiquetas-overlay"></div>

<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/etiquetas.js"></script>
<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/tabs.js"></script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
