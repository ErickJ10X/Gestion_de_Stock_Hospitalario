<?php

use controller\UsuarioController;
use util\AuthGuard;

session_start();
require_once(__DIR__ . '/../../controller/AuthController.php');
require_once(__DIR__ . '/../../controller/UsuarioController.php');
require_once(__DIR__ . '/../../util/AuthGuard.php');

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

<div class="container mt-4">
    <h2 class="mb-4">Administración de Usuarios</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Operación realizada correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h3 class="mb-0">Listado de Usuarios</h3>
            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/register.php" class="btn btn-success">
                <i class="bi bi-person-plus"></i> Registrar Nuevo Usuario
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
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
                                <td><?php echo htmlspecialchars((string)$user->getId()); ?></td>
                                <td><?php echo htmlspecialchars($user->getNombre()); ?></td>
                                <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        <?php 
                                        $rol = $user->getRol();
                                        echo htmlspecialchars((is_object($rol) || is_array($rol)) ? json_encode($rol) : (string)$rol); 
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user->getId() != $_SESSION['id']): ?>
                                        <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/admin/dashboard.php"
                                              method="post" class="d-inline">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?php echo $user->getId(); ?>">
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('¿Eliminar este usuario permanentemente?')">
                                                <i class="bi bi-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">No hay usuarios para mostrar</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include('../templates/footer.php'); ?>
