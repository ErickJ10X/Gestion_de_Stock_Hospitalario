<?php
session_start();
include('../src/view/templates/header.php');
?>

<div class="container main__container mt-5">
    <div class="row main__row justify-content-center">
        <div class="col-md-8 main__content text-center">
            <?php if (isset($_SESSION['usuario'])): ?>
                <h1 class="main__title mb-4">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?>!</h1>
                <div class="card main__card shadow-sm">
                    <div class="card-body main__card-body">
                        <p class="lead main__lead">Has iniciado sesión correctamente.</p>
                        <div class="d-flex main__actions justify-content-center gap-3 mt-4">
                            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/user/profile.php" class="btn btn-primary main__btn main__btn--profile">
                                <i class="bi bi-person-circle"></i> Ver mi perfil
                            </a>
                            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/logout.php" class="btn btn-outline-danger main__btn main__btn--logout">
                                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <h1 class="main__title mb-4">Bienvenido</h1>
                <div class="card main__card shadow-sm">
                    <div class="card-body main__card-body">
                        <p class="lead main__lead">Por favor ingrese a su cuenta</p>
                        <div class="d-flex main__actions justify-content-center gap-3 mt-4">
                            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/login.php" class="btn btn-primary main__btn main__btn--login">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
                            </a>
                            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/register.php" class="btn btn-outline-primary main__btn main__btn--register">
                                <i class="bi bi-person-plus"></i> Registrarse
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('../src/view/templates/footer.php'); ?>
