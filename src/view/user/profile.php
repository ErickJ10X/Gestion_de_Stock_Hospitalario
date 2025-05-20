<?php
session_start();
require_once(__DIR__ . '/../../controller/authController.php');
require_once(__DIR__ . '/../../service/userService.php');
require_once(__DIR__ . '/../../util/authGuard.php');

$authGuard = new AuthGuard();
$authGuard->requireAuth();

include('../templates/header.php');
$authController = new authController();
$authController->viewProfile();
?>

<div>
    <h2>Mi Perfil</h2>

    <?php if (isset($_GET['success'])): ?>
        <div>
            <?php echo isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Operación exitosa.'; ?>
        </div>
    <?php endif; ?>

    <?php
    try {
        $userService = new UserService();
        $user = $userService->getUserByUsername($_SESSION['usuario']);

        if ($user): ?>
            <div>
                <div>
                    <p>
                        <strong>Nombre:</strong> <?php echo htmlspecialchars($user['nombre']); ?></p>
                    <p>
                        <strong>Apellido:</strong> <?php echo htmlspecialchars($user['apellido']); ?></p>
                    <p>
                        <strong>Usuario:</strong> <?php echo htmlspecialchars($user['usuario']); ?></p>
                    <p>
                        <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p>
                        <strong>Rol:</strong> <?php echo htmlspecialchars($user['rol']); ?></p>
                    <div>
                        <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/user/edit_profile.php">Editar Perfil</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div>Usuario no encontrado.</div>
        <?php endif;
    } catch (PDOException $e) {
        echo "<div>Error al cargar el perfil: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    ?>
</div>

<?php include('../templates/footer.php'); ?>

