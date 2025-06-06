<?php
session_start();
require_once(__DIR__ . '/../../controller/HospitalController.php');
require_once(__DIR__ . '/../../controller/PlantaController.php');
require_once(__DIR__ . '/../../controller/AlmacenesController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\AlmacenesController;
use controller\HospitalController;
use controller\PlantaController;
use util\Session;
use util\AuthGuard;

$hospitalController = new HospitalController();
$plantaController = new PlantaController();
$almacenesController = new AlmacenesController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireGestorPlanta();

if ($_SESSION['rol'] === 'Administrador' || $_SESSION['rol'] === 'Gestor general') {
    $hospitales = $hospitalController->index()['hospitales'] ?? [];
    $plantas = $plantaController->index()['plantas'] ?? [];
    $almacenes = $almacenesController->index();
}
if ($_SESSION['rol'] === 'Gestor de hospital') {
    $hospitales = $hospitalController->getByHospital()['hospitales'] ?? [];
    $plantas = $plantaController->getByHospital($_SESSION['idHospital'])['plantas'] ?? [];
    $almacenes = $almacenesController->getByHospital($_SESSION['idHospital']);
}
if ($_SESSION['rol'] === 'Gestor de planta') {
    $hospitales = $hospitalController->getByPlanta($_SESSION['idPlanta'])['hospitales'] ?? [];
    $plantas = $plantaController->getById($_SESSION['idPlanta'])['planta'] ?? [];
    $almacenes = $almacenesController->getByPlanta($_SESSION['idPlanta']);
}

$pageTitle = "Gestión de Almacenes";
include_once(__DIR__ . '/../templates/header.php');
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css?v=<?= time() ?>">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/tabs.css?v=<?= time() ?>">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/almacenes.css?v=<?= time() ?>">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/paginacion.css?v=<?= time() ?>">

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
        <button class="tab-btn <?= !isset($_GET['tab']) || $_GET['tab'] == 'almacenes' ? 'active' : '' ?>"
                data-tab="tab-almacenes">Almacenes
        </button>
        <button class="tab-btn <?= isset($_GET['tab']) && $_GET['tab'] == 'agregar-editar' ? 'active' : '' ?>"
                data-tab="tab-agregar-editar">Agregar/Editar
        </button>
    </div>

    <div class="tab-content">
        <div id="tab-almacenes"
             class="tab-pane <?= !isset($_GET['tab']) || $_GET['tab'] == 'almacenes' ? 'active' : '' ?>">
            <?php include_once(__DIR__ . '/almacenes_tab.php'); ?>
        </div>
        <div id="tab-agregar-editar"
             class="tab-pane <?= isset($_GET['tab']) && $_GET['tab'] == 'agregar-editar' ? 'active' : '' ?>">
            <?php include_once(__DIR__ . '/agregarEditar_tab.php'); ?>
        </div>
    </div>

</div>


<div class="hospital-overlay"></div>

<script>
    // Cerrar alertas
    document.querySelectorAll('.list-alert__close').forEach(button => {
        button.addEventListener('click', function () {
            this.closest('.list-alert').remove();
        });
    });
</script>

<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/tabs.js?v=<?= time() ?>"></script>
<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/almacenes.js?v=<?= time() ?>"></script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
