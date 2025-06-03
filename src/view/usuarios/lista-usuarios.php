<?php
session_start();
require_once(__DIR__ . '/../../controller/UsuarioController.php');
require_once(__DIR__ . '/../../model/enum/RolEnum.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');
require_once(__DIR__ . '/../../controller/Usuario_UbicacionesController.php');

use controller\Usuario_UbicacionesController;
use controller\UsuarioController;
use src\enum\RolEnum;
use util\AuthGuard;
use util\Session;

$usuarioController = new UsuarioController();
$usuarioUbicacionController = new Usuario_UbicacionesController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireAdministrador();

$usuarios = $usuarioController->getAllUsers();
$ubicaciones = $usuarioUbicacionController->getAllUsuarioUbicaciones();

$pageTitle = "Usuarios";
include_once(__DIR__ . '/../templates/header.php');
?>

<!-- Incluir estilos CSS con rutas absolutas y forzar recarga con parámetro de versión -->
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css?v=<?= time() ?>">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/card-form.css?v=<?= time() ?>">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/tabs.css?v=<?= time() ?>">
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/usuarios.css?v=<?= time() ?>">

<!-- jQuery y DataTables primero para evitar problemas de carga -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

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
            <button class="tab-btn active" data-tab="tab-usuarios">Usuarios</button>
            <button class="tab-btn" data-tab="tab-ubicaciones">Ubicaciones</button>
            <button class="tab-btn" data-tab="tab-roles">Roles</button>
        </div>

        <div class="tab-content">
            <div id="tab-usuarios" class="tab-pane active">
                <?php include_once(__DIR__ . '/usuarios_tab.php'); ?>
            </div>

            <div id="tab-ubicaciones" class="tab-pane">
                <?php include_once(__DIR__ . '/ubicaciones_tab.php'); ?>
            </div>

            <div id="tab-roles" class="tab-pane">
                <?php include_once(__DIR__ . '/roles_tab.php'); ?>
            </div>
        </div>
    </div>
</div>

<!-- Overlay para ventanas modales -->
<div class="usuario-overlay"></div>

<?php
function getBadgeColorForRole($role)
{
    switch ($role) {
        case RolEnum::ADMINISTRADOR:
            return 'danger';
        case RolEnum::GESTOR_GENERAL:
            return 'primary';
        case RolEnum::GESTOR_HOSPITAL:
            return 'success';
        case RolEnum::GESTOR_PLANTA:
            return 'info';
        case RolEnum::USUARIO_BOTIQUIN:
            return 'secondary';
        default:
            return 'dark';
    }
}

function getTipoUbicacion($tipo)
{
    switch ($tipo) {
        case 'hospital':
            return 'Hospital';
        case 'planta':
            return 'Planta';
        case 'botiquin':
            return 'Botiquín';
        default:
            return ucfirst($tipo);
    }
}
?>

<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/usuario-cards.js?v=<?= time() ?>"></script>
<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/tabs.js?v=<?= time() ?>"></script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
