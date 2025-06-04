<?php
session_start();
require_once(__DIR__ . '/../../controller/UsuarioController.php');
require_once(__DIR__ . '/../../model/enum/RolEnum.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\UsuarioController;
use util\AuthGuard;
use util\Session;

// Inicializar controladores y utilidades
$usuarioController = new UsuarioController();
$session = new Session();
$authGuard = new AuthGuard();

// Verificar permisos
$authGuard->requireAdministrador();

// Obtener datos para la vista desde el controlador
$viewData = $usuarioController->prepareDataForView();
$usuarios = $viewData['usuarios'] ?? [];
$roles = $viewData['roles'] ?? [];

// Comprobar si hay un usuario para editar
$usuarioEditar = $viewData['usuario_editar'] ?? null;

$pageTitle = "Gestión de Usuarios";
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
            <button class="tab-btn" data-tab="tab-crear-editar">Crear/Editar</button>
            <button class="tab-btn" data-tab="tab-ubicaciones">Asignar Ubicaciones</button>
        </div>

        <div class="tab-content">
            <div id="tab-usuarios" class="tab-pane ">
                <?php include_once(__DIR__ . '/listUsers_tab.php'); ?>
            </div>

            <div id="tab-crear-editar" class="tab-pane">
                <?php include_once(__DIR__ . '/crearEditar_tab.php'); ?>
            </div>

            <div id="tab-ubicaciones" class="tab-pane active">
                <?php include_once(__DIR__ . '/asignarUbicaciones_tab.php'); ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Inicializar variables JavaScript con datos del servidor
    const usuarioEditar = <?= $usuarioEditar ? json_encode($usuarioEditar->toArray()) : 'null' ?>;
    
    // Si hay usuario para editar, activar la pestaña de crear/editar
    if (usuarioEditar) {
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('[data-tab="tab-crear-editar"]').click();
        });
    }
</script>

<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/usuario-cards.js?v=<?= time() ?>"></script>
<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/tabs.js?v=<?= time() ?>"></script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
