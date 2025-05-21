<?php
session_start();
include('../src/view/templates/header.php');
require_once('../src/model/enum/RolEnum.php');
use model\enum\RolEnum;
?>

<div>
    <div>
        <div>
            <?php if (isset($_SESSION['id'])): ?>
                <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h1>
                <div>
                    <div>
                        <p>Has iniciado sesión correctamente.</p>
                        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === RolEnum::ADMINISTRADOR): ?>
                            <p>Tienes acceso a todas las funciones de administrador.</p>
                        <?php endif; ?>
                        <div>
                            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/user/profile.php" class="btn btn-primary main__btn main__btn--profile">
                                <i></i> Ver mi perfil
                            </a>
                            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/logout.php" class="btn btn-outline-danger main__btn main__btn--logout">
                                <i></i> Cerrar sesión
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <h1>Bienvenido</h1>
                <div>
                    <div>
                        <p>Por favor ingrese a su cuenta</p>
                        <div>
                            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/login.php" class="btn btn-primary main__btn main__btn--login">
                                <i></i> Iniciar sesión
                            </a>
                            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/register.php" class="btn btn-outline-primary main__btn main__btn--register">
                                <i></i> Registrarse
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('../src/view/templates/footer.php'); ?>
