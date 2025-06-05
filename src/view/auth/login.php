<?php

use controller\AuthController;
use util\AuthGuard;

session_start();
require_once(__DIR__ . '/../../controller/AuthController.php');
require_once(__DIR__ . '/../../util/AuthGuard.php');
require_once(__DIR__ . '/../../model/enum/RolEnum.php');

$authGuard = new AuthGuard();
$authGuard->requireNoAuth();
$authController = new AuthController();

// No procesamos el login aquí, AuthController ya tiene lógica para POST
// Solo si se envía el formulario, se llamará a login()
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->login();
    // Si llegamos aquí, hubo un error (ya que login() redirige en caso exitoso)
}

// Agregar una clase al body para aplicar estilos específicos de login
$pageClass = 'login-page';
include('../templates/header.php');
?>
<!-- Enlazar el archivo CSS específico para la página de login -->
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/login.css">

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h2>Iniciar Sesión</h2>
        </div>
        <div class="login-body">
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo isset($_GET['message']) ? htmlspecialchars(urldecode($_GET['message'])) : 'Credenciales incorrectas. Por favor, inténtalo de nuevo.'; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php echo isset($_GET['message']) ? htmlspecialchars(urldecode($_GET['message'])) : 'Operación completada con éxito.'; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['warning'])): ?>
                <div class="alert alert-warning">
                    <?php echo isset($_GET['message']) ? htmlspecialchars(urldecode($_GET['message'])) : 'Atención: se requiere acción.'; ?>
                </div>
            <?php endif; ?>

            <div class="login-logo">
                <!-- Puedes añadir un logo aquí -->
                <!-- <img src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/img/logo.png" alt="Pegasus Medical Logo"> -->
            </div>
            
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/login.php" method="post" class="login-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Introduce tu email" required>
                </div>
                <div class="form-group">
                    <label for="contrasena">Contraseña</label>
                    <input type="password" class="form-control" name="contrasena" id="contrasena" placeholder="Introduce tu contraseña" required>
                </div>
                <button type="submit" class="btn btn-login">Iniciar Sesión</button>
            </form>
        </div>
    </div>
</div>

<?php include('../templates/footer.php'); ?>