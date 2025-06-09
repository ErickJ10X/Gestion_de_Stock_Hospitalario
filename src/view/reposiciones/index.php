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

$authGuard->requireGestorHospital();

// Para depuración
$debugMode = false;

// Procesar formularios si se han enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['action'] ?? '';

    if ($debugMode) {
        error_log("Acción POST recibida: " . $accion);
        error_log("Datos POST: " . print_r($_POST, true));
    }

    if ($accion === 'crear') {
        $resultado = $reposicionesController->procesarFormularioCrear();
        // Redirigir para evitar reenvío de formulario
        header("Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/reposiciones/?tab=ver-estado");
        exit;
    } else if ($accion === 'marcar_entregadas') {
        $resultado = $reposicionesController->procesarFormularioMarcarEntregadas();

        if ($debugMode) {
            error_log("Resultado de marcar como entregadas: " . print_r($resultado, true));
        }

        // Redirigir para evitar reenvío de formulario
        header("Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/reposiciones/?tab=marcar-entregado");
        exit;
    } else if ($accion === 'marcar_entregada') {
        // Procesar la acción de marcar una sola reposición como entregada
        if (isset($_POST['id_reposicion'])) {
            $id = intval($_POST['id_reposicion']);
            $resultado = $reposicionesController->marcarComoCompletada($id);

            if ($debugMode) {
                error_log("Resultado de marcar una reposición como completada: " . print_r($resultado, true));
            }

            // Redirigir para evitar reenvío de formulario
            header("Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/reposiciones/?tab=ver-estado");
            exit;
        }
    } else if ($accion === 'marcar_pendiente') {
        // Procesar la acción de marcar una reposición como pendiente
        if (isset($_POST['id_reposicion'])) {
            $id = intval($_POST['id_reposicion']);
            $resultado = $reposicionesController->marcarComoPendiente($id);
            // Redirigir para evitar reenvío de formulario
            header("Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/reposiciones/?tab=ver-estado");
            exit;
        }
    }
}

// Cargar datos para las vistas
// Primero cargamos todas las reposiciones
$reposiciones = $reposicionesController->index()['reposiciones'] ?? [];

// Cargar productos, almacenes y botiquines
$productos = $productoController->index()['productos'] ?? [];
$almacenes = $almacenesController->index() ?? [];
$botiquines = $botiquinController->index()['botiquines'] ?? [];

// Determinar la pestaña activa
$activeTab = 'tab-ver-estado';
if (isset($_GET['tab'])) {
    switch ($_GET['tab']) {
        case 'generar-reposicion':
            $activeTab = 'tab-generar-reposicion';
            break;
        case 'marcar-entregado':
            $activeTab = 'tab-marcar-entregado';
            break;
        default:
            $activeTab = 'tab-ver-estado';
    }
}

$pageTitle = "Reposiciones";
include_once(__DIR__ . '/../templates/header.php');
?>

<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/tabs.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/hospitales.css">

<!-- Font Awesome para íconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

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

    <?php if ($session->hasMessage('warning')): ?>
        <div class="list-alert list-alert--warning">
            <p class="list-alert__message"><?= $session->getMessage('warning') ?></p>
            <button type="button" class="list-alert__close">&times;</button>
        </div>
        <?php $session->clearMessage('warning'); ?>
    <?php endif; ?>

    <?php if ($debugMode): ?>
        <div class="list-alert list-alert--info">
            <p class="list-alert__message">Reposiciones cargadas: <?= count($reposiciones) ?></p>
            <button type="button" class="list-alert__close">&times;</button>
        </div>
    <?php endif; ?>

    <div class="tabs-container">
        <div class="tabs-nav">
            <button class="tab-btn <?= $activeTab === 'tab-ver-estado' ? 'active' : '' ?>" data-tab="tab-ver-estado">Ver Estado</button>
            <button class="tab-btn <?= $activeTab === 'tab-generar-reposicion' ? 'active' : '' ?>" data-tab="tab-generar-reposicion">Generar Reposición</button>
            <button class="tab-btn <?= $activeTab === 'tab-marcar-entregado' ? 'active' : '' ?>" data-tab="tab-marcar-entregado">Marcar como Entregado</button>
        </div>

        <div class="tab-content">
            <div id="tab-ver-estado" class="tab-pane <?= $activeTab === 'tab-ver-estado' ? 'active' : '' ?>">
                <?php include_once(__DIR__ . '/ver_estado_tab.php'); ?>
            </div>
            <div id="tab-generar-reposicion" class="tab-pane <?= $activeTab === 'tab-generar-reposicion' ? 'active' : '' ?>">
                <?php include_once(__DIR__ . '/generar_reposicion_tab.php'); ?>
            </div>
            <div id="tab-marcar-entregado" class="tab-pane <?= $activeTab === 'tab-marcar-entregado' ? 'active' : '' ?>">
                <?php include_once(__DIR__ . '/marcar_entregado_tab.php'); ?>
            </div>
        </div>
    </div>
</div>

<div class="reposicion-overlay"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tabs
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabPanes = document.querySelectorAll('.tab-pane');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');

                // Actualizar URL con el parámetro tab
                const tabName = tabId.replace('tab-', '');
                const url = new URL(window.location.href);
                url.searchParams.set('tab', tabName);
                history.pushState({}, '', url);

                // Activar tab
                tabBtns.forEach(b => b.classList.remove('active'));
                tabPanes.forEach(p => p.classList.remove('active'));

                this.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Cerrar alertas
        document.querySelectorAll('.list-alert__close').forEach(function(closeBtn) {
            closeBtn.addEventListener('click', function() {
                this.parentElement.style.display = 'none';
            });
        });
    });
</script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
