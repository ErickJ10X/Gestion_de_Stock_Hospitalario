<?php

use controller\AuthController;
use controller\UsuarioController;
use util\AuthGuard;

session_start();
require_once(__DIR__ . '/../../controller/AuthController.php');
require_once(__DIR__ . '/../../controller/UsuarioController.php');
require_once(__DIR__ . '/../../util/AuthGuard.php');

$authGuard = new AuthGuard();
$authGuard->requireAuth();

$usuarioController = new UsuarioController();
$authController = new AuthController();
$user = $usuarioController->getUserById($_SESSION['id']);
$ubicaciones = $usuarioController->getUbicacionesUsuario($_SESSION['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController->updateUserProfile();
}

include('../templates/header.php');
?>

<div>
    <h2>Editar Perfil</h2>

    <?php if (isset($error_message)): ?>
        <div>
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <div>
            <?php echo isset($_GET['message']) ? htmlspecialchars(urldecode($_GET['message'])) : 'Error al actualizar el perfil.'; ?>
        </div>
    <?php endif; ?>

    <?php if ($user): ?>
        <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/user/edit_profile.php" method="post" class="main__form">
            <div>
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($user->getNombre()); ?>" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user->getEmail()); ?>" required>
            </div>
            <div>
                <label for="current_password">Contraseña actual (solo si deseas cambiarla):</label>
                <input type="password" name="current_password">
            </div>
            <div>
                <label for="new_password">Nueva Contraseña (dejar en blanco para no cambiar):</label>
                <input type="password" name="new_password">
            </div>
            <div>
                <label for="confirm_new_password">Confirmar Nueva Contraseña:</label>
                <input type="password" name="confirm_new_password">
            </div>

            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user->getId()); ?>">

            <h3>Ubicaciones asignadas:</h3>
            <ul>
                <?php if (!empty($ubicaciones)): ?>
                    <?php foreach ($ubicaciones as $ubicacion): ?>
                        <li>
                            <?php 
                                echo htmlspecialchars($ubicacion['tipo']) . ': ' . 
                                     htmlspecialchars($ubicacion['nombre']); 
                            ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No tienes ubicaciones asignadas.</li>
                <?php endif; ?>
            </ul>
            <p><em>Para modificar tus ubicaciones, contacta con un administrador.</em></p>

            <button type="submit">Guardar Cambios</button>
            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/user/profile.php">Cancelar</a>
        </form>
    <?php else: ?>
        <div>Usuario no encontrado.</div>
    <?php endif; ?>
</div>

<?php include('../templates/footer.php'); ?>
