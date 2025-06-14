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
$viewData = $usuarioController->index();
$usuarios = $viewData['usuarios'] ?? [];
$usuario_editar = $viewData['usuario_editar'] ?? null;
$roles = $viewData['roles'] ?? [];

// Si no se obtienen usuarios, inicializar como array vacío
if (!is_array($usuarios)) {
    $usuarios = [];
}

$pageTitle = "Gestión de Usuarios";
include_once(__DIR__ . '/../templates/header.php');
?>

    <!-- Incluir estilos CSS con rutas absolutas y forzar recarga con parámetro de versión -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet"
          href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css?v=<?= time() ?>">
    <link rel="stylesheet"
          href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/tabs.css?v=<?= time() ?>">
    <link rel="stylesheet"
          href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/usuarios.css?v=<?= time() ?>">
    <link rel="stylesheet"
          href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/paginacion.css?v=<?= time() ?>">


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
            <button class="tab-btn <?= !isset($_GET['tab']) || $_GET['tab'] == 'usuarios' ? 'active' : '' ?>"
                    data-tab="tab-usuarios">Usuarios
            </button>
            <button class="tab-btn <?= isset($_GET['tab']) && $_GET['tab'] == 'crear-editar' ? 'active' : '' ?>"
                    data-tab="tab-crear-editar">Agregar/Editar
            </button>
            <button class="tab-btn <?= isset($_GET['tab']) && $_GET['tab'] == 'asignar-ubicaciones' ? 'active' : '' ?>"
                    data-tab="tab-ubicaciones">Asignar Ubicaciones
            </button>
        </div>

        <div class="tab-content">
            <div id="tab-usuarios"
                 class="tab-pane <?= !isset($_GET['tab']) || $_GET['tab'] == 'usuarios' ? 'active' : '' ?>">
                <?php include_once(__DIR__ . '/usuarios_tab.php'); ?>
            </div>

            <div id="tab-crear-editar"
                 class="tab-pane <?= isset($_GET['tab']) && $_GET['tab'] == 'crear-editar' ? 'active' : '' ?>">
                <?php include_once(__DIR__ . '/agregarEditar_tab.php'); ?>
            </div>

            <div id="tab-ubicaciones"
                 class="tab-pane <?= isset($_GET['tab']) && $_GET['tab'] == 'asignar-ubicaciones' ? 'active' : '' ?>">
                <?php include_once(__DIR__ . '/asignarUbicaciones_tab.php'); ?>
            </div>
        </div>
    </div>


    <script>
        // Inicializar variables JavaScript con datos del servidor
        const usuarioEditar = <?= $usuario_editar ? json_encode([
            'id_usuario' => $usuario_editar->getIdUsuario(),
            'nombre' => $usuario_editar->getNombre(),
            'email' => $usuario_editar->getEmail(),
            'rol' => $usuario_editar->getRol(),
            'activo' => $usuario_editar->isActivo()
        ]) : 'null' ?>;

        // Cerrar alertas
        document.querySelectorAll('.list-alert__close').forEach(button => {
            button.addEventListener('click', function () {
                this.closest('.list-alert').remove();
            });
        });
    </script>

    <script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/tabs.js?v=<?= time() ?>"></script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>