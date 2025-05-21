<?php

use controller\AuthController;
use util\AuthGuard;

session_start();
require_once(__DIR__ . '/../../controller/AuthController.php');
require_once(__DIR__ . '/../../util/AuthGuard.php');

$authGuard = new AuthGuard();
$authGuard->requireNoAuth();
$authController = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->login();
}

include('../templates/header.php');
?>
    <div>
        <div>
            <div>
                <?php if (isset($_GET['error'])): ?>
                    <div>
                        <?php echo isset($_GET['message']) ? htmlspecialchars(urldecode($_GET['message'])) : 'Credenciales incorrectas. Por favor, inténtalo de nuevo.'; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['success'])): ?>
                    <div>
                        <?php echo isset($_GET['message']) ? htmlspecialchars(urldecode($_GET['message'])) : 'Operación completada con éxito.'; ?>
                    </div>
                <?php endif; ?>
                
                <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/login.php" method="post" class="main__form">
                    <div>
                        <label for="email">Email</label>
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    <div>
                        <label for="contrasena">Contraseña</label>
                        <input type="password" name="contrasena" placeholder="Contraseña" required>
                    </div>
                    <button type="submit">Iniciar Sesión</button>
                </form>
                <p>¿No tienes cuenta? <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/register.php" class="main__form-link">Regístrate</a></p>
            </div>
        </div>
    </div>
<?php include('../templates/footer.php'); ?>
