<?php
session_start();
require_once(__DIR__ . '/../../controller/authController.php');
require_once(__DIR__ . '/../../util/authGuard.php');

$authGuard = new AuthGuard();
$authGuard->requireNoAuth();
$authController = new authController();
$authController->login();

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
                
                <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/login.php" method="post" class="main__form">
                    <div>
                        <label for="usuario">Usuario</label>
                        <input type="text" name="usuario" placeholder="Usuario" required>
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
