<?php
session_start();
require_once(__DIR__ . '/../../controller/AuthController.php');
require_once(__DIR__ . '/../../controller/UsuarioController.php');
require_once(__DIR__ . '/../../util/authGuard.php');

$authGuard = new AuthGuard();
$authGuard->requireAdmin();

$usuarioController = new UsuarioController();
$usuarios = $usuarioController->getAllUsers();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['user_id'])) {
    $userId = (int)$_POST['user_id'];
    
    if ($userId === (int)$_SESSION['id']) {
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/admin/dashboard.php?error=no_self_delete');
        exit;
    }
    
    try {
        $result = $usuarioController->deleteUser($userId);
        if ($result) {
            header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/admin/dashboard.php?success=1');
            exit;
        } else {
            header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/admin/dashboard.php?error=delete_failed');
            exit;
        }
    } catch (Exception $e) {
        header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/admin/dashboard.php?error=delete_failed&message=' . urlencode($e->getMessage()));
        exit;
    }
}

include('../templates/header.php');
?>

<div>
    <h2>Administración de Usuarios</h2>

    <?php if (isset($_GET['success'])): ?>
        <div>Operación realizada correctamente.</div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div>
            <?php
            if (isset($_GET['message'])) {
                echo htmlspecialchars(urldecode($_GET['message']));
            } else {
                switch ($_GET['error']) {
                    case 'no_self_delete':
                        echo "No puedes eliminarte a ti mismo.";
                        break;
                    case 'delete_failed':
                        echo "Error al eliminar usuario: " . ($_GET['message'] ?? '');
                        break;
                    default:
                        echo "Error desconocido.";
                }
            }
            ?>
        </div>
    <?php endif; ?>

    <div>
        <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/register.php" class="btn btn-primary">Registrar Nuevo Usuario</a>
        
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($usuarios)): ?>
                <?php foreach ($usuarios as $user): ?>
                    <tr>
                        <td><?php echo $user->getId(); ?></td>
                        <td><?php echo htmlspecialchars($user->getNombre()); ?></td>
                        <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                        <td>
                            <span>
                                <?php echo htmlspecialchars($user->getRolValue()); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($user->getId() != $_SESSION['id']): ?>
                                <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/admin/dashboard.php"
                                      method="post">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="user_id" value="<?php echo $user->getId(); ?>">
                                    <button type="submit"
                                            onclick="return confirm('¿Eliminar este usuario permanentemente?')">
                                        <i></i> Eliminar
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">No hay usuarios para mostrar</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('../templates/footer.php'); ?>
