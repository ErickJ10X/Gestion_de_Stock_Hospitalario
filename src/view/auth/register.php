<?php

use controller\AuthController;
use util\AuthGuard;
use model\enum\RolEnum;

session_start();
require_once(__DIR__ . '/../../controller/AuthController.php');
require_once(__DIR__ . '/../../util/AuthGuard.php');
require_once(__DIR__ . '/../../model/service/UsuarioService.php');
require_once(__DIR__ . '/../../model/enum/RolEnum.php');

$authGuard = new AuthGuard();
$authGuard->requireNoAuth();
$authController = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->register();
}

$userService = new model\service\UsuarioService();
$rolOptions = $userService->getRolOptions();

include('../templates/header.php');
?>

    <div>
        <div>
            <div>
                <h2>Registro de Usuario</h2>

                <?php if (isset($_GET['error'])): ?>
                    <div>
                        <?php
                        if (isset($_GET['message'])) {
                            echo htmlspecialchars(urldecode($_GET['message']));
                        } elseif ($_GET['error'] == 'usuario_existente') {
                            echo "El email ya está en uso.";
                        } else {
                            echo "Error en el registro.";
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/register.php" method="post" class="main__form">
                    <div>
                        <label for="nombre">Nombre:</label>
                        <input type="text" name="nombre" required>
                    </div>
                    <div>
                        <label for="email">Email:</label>
                        <input type="email" name="email" required>
                    </div>
                    <div>
                        <label for="contrasena">Contraseña: (mínimo 8 caracteres, al menos una letra mayuscula, una letra minuscula, un numero y un caracter especial)</label>
                        <input type="password" name="contrasena" required>
                    </div>
                    <div>
                        <label for="confirmar_contrasena">Confirmar Contraseña:</label>
                        <input type="password" name="confirmar_contrasena" required>
                    </div>
                    
                    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === RolEnum::ADMINISTRADOR): ?>
                    <div>
                        <label for="rol">Rol:</label>
                        <select name="rol">
                            <?php foreach ($rolOptions as $rol): ?>
                                <option value="<?php echo htmlspecialchars($rol); ?>">
                                    <?php echo htmlspecialchars($rol); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <button type="submit">Registrarse</button>
                </form>

                <p>¿Ya tienes cuenta? <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/login.php" class="main__form-link">Inicia Sesión</a></p>
            </div>
        </div>
    </div>

<?php include('../templates/footer.php'); ?>
