<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '/../../model/enum/RolEnum.php');
require_once(__DIR__ . '/../../util/Session.php');

use model\enum\RolEnum;
use util\Session;

// Usar la clase Session para manejar los datos del usuario de forma más segura
$session = new Session();
$isLoggedIn = $session->isLoggedIn();
$userName = '';
$userRole = '';

if ($isLoggedIn) {
    // Mantener compatibilidad con el código existente
    if (!isset($_SESSION['id']) && $session->getUserData('id')) {
        $_SESSION['id'] = $session->getUserData('id');
        $_SESSION['nombre'] = $session->getUserData('nombre');
        $_SESSION['email'] = $session->getUserData('email');
        $_SESSION['rol'] = $session->getUserData('rol');
    }
    
    $userName = htmlspecialchars($_SESSION['nombre'] ?? $session->getUserData('nombre') ?? 'Usuario');
    $userRole = $_SESSION['rol'] ?? $session->getUserData('rol') ?? '';
}

// Comprobar si se ha pasado una clase de página
$pageClass = isset($pageClass) ? $pageClass : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor Hospitalario</title>
    <!-- Incorporar todos los archivos CSS disponibles -->
    <link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/header.css">
    <link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/footer.css">
    <link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/tabs.css">
    <link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/list.css">
    <link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/catalogo-productos.css">
    <link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/usuarios.css">
    <link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/login.css">
    <link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="<?= $pageClass ?>">
<header class="header">
    <div class="container header__container">
        <?php if (!$isLoggedIn): ?>
            <a class="header__brand" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public">Pegasus Medical</a>
        <?php else: ?>
            <a class="header__brand" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public">Pegasus</a>
        <?php endif; ?>
        <button class="header__toggle" id="navToggle">
            <i class="bi bi-list"></i>
        </button>
        
        <nav class="nav" id="mainNav">
            <ul class="nav__list">
                <?php if (!$isLoggedIn): ?>
                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public">Inicio</a>
                    </li>
                <?php else: ?>
                    <?php if ($userRole === RolEnum::ADMINISTRADOR): ?>
                        <li class="nav__item">
                            <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/usuarios">Usuarios</a>
                        </li>
                    <?php endif; ?>

                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales">Hospitales</a>
                    </li>

                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes">Almacenes</a>
                    </li>

                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines">Botiquines</a>
                    </li>

                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos">Productos</a>
                    </li>

                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/pactos">Pactos</a>
                    </li>
                    
                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/lecturaStock">Stock</a>
                    </li>
                    
                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/reposiciones">Reposiciones</a>
                    </li>
                    
                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/etiquetas">Etiquetas</a>
                    </li>
                    
                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/informes">Informes</a>
                    </li>

                <?php endif; ?>
            </ul>

            <ul class="nav__list nav__user-list">
                <?php if ($isLoggedIn): ?>
                    <li class="nav__item nav__dropdown">
                        <a class="nav__link nav__dropdown-toggle" href="#" id="userDropdown">
                            <i class="bi bi-person-circle"></i> <?= $userName ?>
                        </a>
                        <div class="nav__dropdown-menu">
                            <a class="nav__dropdown-item" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/user/profile.php">Perfil</a>
                            <div class="nav__dropdown-divider"></div>
                            <a class="nav__dropdown-item" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/logout.php">Cerrar Sesión</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/login.php">Iniciar Sesión</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <div class="container">
        <?php if (isset($_GET['error']) && isset($_GET['message'])): ?>
            <div class="alert alert--danger">
                <?= htmlspecialchars(urldecode($_GET['message'])) ?>
                <button type="button" class="alert__close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success']) && isset($_GET['message'])): ?>
            <div class="alert alert--success">
                <?= htmlspecialchars(urldecode($_GET['message'])) ?>
                <button type="button" class="alert__close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>
    </div>
</header>

<main class="main container">

<script>
// Toggle para el menú móvil
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.getElementById('navToggle');
    const mainNav = document.getElementById('mainNav');
    
    if (navToggle) {
        navToggle.addEventListener('click', function() {
            mainNav.classList.toggle('nav--active');
        });
    }
    
    // Cerrar alertas
    const alertCloseButtons = document.querySelectorAll('.alert__close');
    alertCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.parentElement.style.display = 'none';
        });
    });
});
</script>