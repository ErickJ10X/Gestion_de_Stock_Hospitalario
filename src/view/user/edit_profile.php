<?php
session_start();
require_once(__DIR__ . '/../../controller/AuthController.php');
require_once(__DIR__ . '/../../controller/UsuarioController.php');
require_once(__DIR__ . '/../../util/authGuard.php');

$authGuard = new AuthGuard();
$authGuard->requireAuth();

$usuarioController = new UsuarioController();
$user = $usuarioController->getUserById($_SESSION['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_SESSION['id'];
    $nombre = $_POST['nombre'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['contrasena'] ?? null;
    
    try {
        $result = $usuarioController->updateProfile($id, $nombre, $email, $password);
        if ($result) {
            header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/user/profile.php?success=1&message=' . urlencode('Perfil actualizado correctamente'));
            exit;
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
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
                <label for="contrasena">Nueva Contraseña (dejar en blanco para no cambiar):</label>
                <input type="password" name="contrasena">
            </div>

            <button type="submit">Guardar Cambios</button>
            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/user/profile.php">Cancelar</a>
        </form>
    <?php else: ?>
        <div>Usuario no encontrado.</div>
    <?php endif; ?>
</div>

<?php include('../templates/footer.php'); ?>
