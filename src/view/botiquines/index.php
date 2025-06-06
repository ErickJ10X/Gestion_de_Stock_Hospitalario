<?php
session_start();
require_once(__DIR__ . '/../../controller/HospitalController.php');
require_once(__DIR__ . '/../../controller/PlantaController.php');
require_once(__DIR__ . '/../../controller/BotiquinController.php');
require_once(__DIR__ . '/../../controller/AlmacenesController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\AlmacenesController;
use controller\HospitalController;
use controller\PlantaController;
use controller\BotiquinController;
use util\Session;
use util\AuthGuard;

$hospitalController = new HospitalController();
$plantaController = new PlantaController();
$botiquinController = new BotiquinController();
$almacenesController = new AlmacenesController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireGestorHospital();

$hospitales = $hospitalController->index()['hospitales'] ?? [];
$plantas = $plantaController->index()['plantas'] ?? [];
$botiquines = $botiquinController->index()['botiquines'] ?? [];
$almacenes = $almacenesController->index();

$pageTitle = "Gestión de Botiquines";
include_once(__DIR__ . '/../templates/header.php');
?>

    <!-- Estilos propios -->
    <link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css">
    <link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/tabs.css">
    <link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/botiquines.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/paginacion.css">


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
            <button class="tab-btn active" data-tab="tab-botiquines">Botiquines</button>
            <button class="tab-btn" data-tab="tab-agregar-editar">Agregar/Editar</button>
        </div>

        <div class="tab-content">
            <div id="tab-botiquines" class="tab-pane active">
                <?php include_once(__DIR__ . '/botiquines_tab.php'); ?>
            </div>

            <div id="tab-agregar-editar" class="tab-pane">
                <?php include_once(__DIR__ . '/agregarEditar_tab.php'); ?>
            </div>
        </div>
    </div>


    <div class="hospital-overlay"></div>

    <!-- Bootstrap JS -->
    <script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/tabs.js"></script>
    <script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/botiquines.js?v=<?= time() ?>"></script>

    <script>
        // Función global para navegación entre pestañas
        function cambiarAPestana(idPestana) {
            const tabBtn = document.querySelector(`.tab-btn[data-tab="${idPestana}"]`);
            if (tabBtn) tabBtn.click();
        }
    </script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
