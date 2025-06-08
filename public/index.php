<?php
session_start();
// Agregar una clase al body para aplicar estilos específicos de la página de inicio
$pageClass = 'home-page';
include('../src/view/templates/header.php');
require_once('../src/model/enum/RolEnum.php');

use model\enum\RolEnum;

?>
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/index.css">

<div class="home-container">
    <div class="welcome-card">
        <div class="welcome-header">
            <?php if (isset($_SESSION['id'])): ?>
                <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h1>
            <?php else: ?>
                <h1>Bienvenido a Pegasus Medical</h1>
            <?php endif; ?>
        </div>
        <div class="welcome-body">
            <?php if (isset($_SESSION['id'])): ?>
                <p>Has iniciado sesión correctamente.</p>
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === RolEnum::ADMINISTRADOR): ?>
                    <p>Tienes acceso a todas las funciones de administrador.</p>
                <?php endif; ?>
                <div class="welcome-actions">
                    <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/user/profile.php" class="main__btn main__btn--profile">
                        <i class="bi bi-person-circle"></i> Ver mi perfil
                    </a>
                    <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/logout.php" class="main__btn main__btn--logout">
                        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                    </a>
                </div>
            <?php else: ?>
                <p>Sistema de gestión de stock hospitalario</p>
                <div class="welcome-actions">
                    <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/login.php" class="main__btn main__btn--login">
                        <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('../src/view/templates/footer.php'); ?>