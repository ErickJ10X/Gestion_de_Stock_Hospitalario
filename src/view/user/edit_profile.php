<?php
session_start();
require_once(__DIR__ . '/../../controller/authController.php');
require_once(__DIR__ . '/../../util/authGuard.php');
require_once(__DIR__ . '/../../service/userService.php');

$authGuard = new AuthGuard();
$authGuard->requireAuth();
$authController = new authController();

include('../templates/header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $authController->updateProfile();
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<div>
    <h2>Editar Perfil</h2>

    <?php if (isset($_GET['success'])): ?>
        <div>Perfil actualizado correctamente.</div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div>
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <div>
            <?php echo isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Error al actualizar el perfil.'; ?>
        </div>
    <?php endif; ?>

    <?php
    try {
        $userService = new UserService();
        $user = $userService->getUserByUsername($_SESSION['usuario']);

        if ($user): ?>
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/user/edit_profile.php" method="post"
                  class="main__form">
                <div>
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre"
                           value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
                </div>
                <div>
                    <label for="apellido">Apellido:</label>
                    <input type="text" name="apellido"
                           value="<?php echo htmlspecialchars($user['apellido']); ?>" required>
                </div>
                <div>
                    <label for="usuario">Nombre de Usuario:</label>
                    <input type="text" name="usuario"
                           value="<?php echo htmlspecialchars($_SESSION['usuario']); ?>" required>
                </div>
                <div>
                    <label for="email">Email:</label>
                    <input type="email" name="email"
                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="mb-3 main__form-group">
                    <label for="contrasena">Nueva Contraseña (dejar en blanco para
                        no
                        cambiar):</label>
                    <input type="password" name="contrasena">
                </div>

                <button type="submit">Guardar Cambios</button>
                <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/user/profile.php">Cancelar</a>
            </form>

        <?php else: ?>
            <div>Usuario no encontrado.</div>
        <?php endif;
    } catch (PDOException $e) {
        echo "
    <div>Error al cargar el perfil: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    ?>
</div>

<?php include('../templates/footer.php'); ?>
