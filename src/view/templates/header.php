<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Stock Hospitalario</title>
</head>
<body>
<header>
    <div>
        <h1>Gestor de Usuarios</h1>
        <nav>
            <ul>
                <li>
                    <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/index.php">Inicio</a>
                </li>
                <?php if (isset($_SESSION['usuario'])): ?>
                    <li>
                        <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/user/profile.php">Perfil</a>
                    </li>
                    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                        <li>
                            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/admin/dashboard.php">Admin Dashboard</a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/logout.php">Salir</a>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/login.php">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
<main>
