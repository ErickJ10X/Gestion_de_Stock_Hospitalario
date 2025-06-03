<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '/../../model/enum/RolEnum.php');

use src\enum\RolEnum;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor Hospitalario</title>
    <link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/header.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
<header class="header">
    <div class="container header__container">
        <?php if (!isset($_SESSION['id'])): ?>
            <a class="header__brand" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/index.php">Pegasus Medical</a>
        <?php else: ?>
            <div class="header__spacer"></div>
        <?php endif; ?>
        <button class="header__toggle" id="navToggle">
            <i class="bi bi-list"></i>
        </button>
        
        <nav class="nav" id="mainNav">
            <ul class="nav__list">
                <?php if (!isset($_SESSION['id'])): ?>
                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/index.php">Inicio</a>
                    </li>
                <?php else: ?>
                    <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] === RolEnum::ADMINISTRADOR)): ?>
                        <li class="nav__item">
                            <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/usuarios/lista-usuarios.php">Usuarios</a>
                        </li>
                    <?php endif; ?>

                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php">Hospitales</a>
                    </li>

                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/lista_productos.php">Productos</a>
                    </li>
                    
                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/lecturaStock/lista_lectura_stock.php">Stock</a>
                    </li>
                    
                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/reposiciones/lista_reposiciones.php">Reposiciones</a>
                    </li>
                    
                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/etiquetas/generar_etiquetas.php">Etiquetas</a>
                    </li>
                    
                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/informes/panel_informes.php">Informes</a>
                    </li>
                    
                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/configuracion/ajustes.php">Configuración</a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="nav__list nav__user-list">
                <?php if (isset($_SESSION['id'])): ?>
                    <li class="nav__item nav__dropdown">
                        <a class="nav__link nav__dropdown-toggle" href="#" id="userDropdown">
                            <i class="bi bi-person-circle"></i> <?= isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : 'Usuario' ?>
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
                    <li class="nav__item">
                        <a class="nav__link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/register.php">Registrarse</a>
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
