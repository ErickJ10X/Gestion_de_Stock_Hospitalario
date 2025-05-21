<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '/../../model/enum/RolEnum.php');
use model\enum\RolEnum;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Stock Hospitalario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .dropdown-menu {
            border-radius: 0;
        }
        .dropdown:hover .dropdown-menu {
            display: block;
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/index.php">Gestor de Stock Hospitalario</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/index.php">Inicio</a>
                    </li>

                    <?php if (isset($_SESSION['id'])): ?>
                        <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] === RolEnum::ADMINISTRADOR || $_SESSION['rol'] === RolEnum::GESTOR_GENERAL || $_SESSION['rol'] === RolEnum::GESTOR_HOSPITAL)): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="hospitalesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Hospitales
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="hospitalesDropdown">
                                    <li><a class="dropdown-item" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php">Lista de Hospitales</a></li>
                                    <li><a class="dropdown-item" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/registrar_hospital.php">Registrar Hospital</a></li>
                                </ul>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="plantasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Plantas
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="plantasDropdown">
                                    <li><a class="dropdown-item" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/listar_plantas.php">Lista de Plantas</a></li>
                                    <li><a class="dropdown-item" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/registrar_planta.php">Registrar Planta</a></li>
                                </ul>
                            </li>
                            
                            <!-- Nuevo menú para Almacenes -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="almacenesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Almacenes
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="almacenesDropdown">
                                    <li><a class="dropdown-item" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/tabla_almacenes.php">Lista de Almacenes</a></li>
                                    <li><a class="dropdown-item" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/crear_almacen.php">Registrar Almacén</a></li>
                                </ul>
                            </li>
                            
                            <!-- Nuevo menú para Botiquines -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="botiquinesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Botiquines
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="botiquinesDropdown">
                                    <li><a class="dropdown-item" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/tabla_botiquines.php">Lista de Botiquines</a></li>
                                    <li><a class="dropdown-item" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/crear_botiquin.php">Registrar Botiquín</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === RolEnum::ADMINISTRADOR): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/admin/dashboard.php">Panel de Administración</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i> <?= isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : 'Usuario' ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/user/profile.php">Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/logout.php">Cerrar Sesión</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/login.php">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/register.php">Registrarse</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-3">
        <?php if (isset($_GET['error']) && isset($_GET['message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars(urldecode($_GET['message'])) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success']) && isset($_GET['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars(urldecode($_GET['message'])) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>
</header>

<main class="container py-4">
