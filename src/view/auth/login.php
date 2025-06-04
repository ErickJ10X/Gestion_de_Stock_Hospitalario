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

include('../templates/header.php');
?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">Iniciar Sesión</h2>
                    </div>
                    <div class="card-body">
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

                        <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/login.php" method="post" class="main__form">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
                            </div>
                            <div class="mb-3">
                                <label for="contrasena" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" name="contrasena" id="contrasena" placeholder="Contraseña" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                            </div>
                        </form>
                        <p class="text-center mt-3">¿No tienes cuenta? <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/register.php" class="main__form-link">Regístrate</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include('../templates/footer.php'); ?>
