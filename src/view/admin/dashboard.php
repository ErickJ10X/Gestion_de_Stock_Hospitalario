<?php
session_start();
require_once(__DIR__ . '/../../controller/authController.php');
require_once(__DIR__ . '/../../util/authGuard.php');

$authGuard = new AuthGuard();
$authGuard->requireAdmin();

include('../templates/header.php');
$authController = new authController();
$stmt = $authController->viewAdminDashboard();
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
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            <?php
            try {
                while ($user = $stmt->fetch(PDO::FETCH_ASSOC)):
                    ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['usuario']); ?></td>
                        <td>
                            <span>
                                <?php echo ucfirst($user['rol']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($user['usuario'] != $_SESSION['usuario'] && $user['rol'] !== 'admin'): ?>
                                <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/admin/dashboard.php"
                                      method="post">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="username"
                                           value="<?php echo htmlspecialchars($user['usuario']); ?>">
                                    <button type="submit"
                                            onclick="return confirm('¿Eliminar este usuario permanentemente?')">
                                        <i></i> Eliminar
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php
                endwhile;
            } catch (PDOException $e) {
                echo "<tr><td colspan='4'>Error al cargar usuarios: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('../templates/footer.php'); ?>
