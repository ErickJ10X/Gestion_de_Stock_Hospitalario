<?php

use controller\UsuarioController;
use util\AuthGuard;

session_start();
require_once(__DIR__ . '/../../controller/AuthController.php');
require_once(__DIR__ . '/../../controller/UsuarioController.php');
require_once(__DIR__ . '/../../util/AuthGuard.php');

$authGuard = new AuthGuard();
$authGuard->requireAuth();

$usuarioController = new UsuarioController();
$user = $usuarioController->getUserById($_SESSION['id']);
$ubicaciones = $usuarioController->getUbicacionesUsuario($_SESSION['id']);

include('../templates/header.php');
?>

<div>
    <h2>Mi Perfil</h2>

    <?php if (isset($_GET['success'])): ?>
        <div>
            <?php echo isset($_GET['message']) ? htmlspecialchars(urldecode($_GET['message'])) : 'Operación exitosa.'; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div>
            <?php echo isset($_GET['message']) ? htmlspecialchars(urldecode($_GET['message'])) : 'Error al procesar la solicitud.'; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['warning'])): ?>
        <div>
            <?php echo isset($_GET['message']) ? htmlspecialchars(urldecode($_GET['message'])) : 'Atención: se requiere acción.'; ?>
        </div>
    <?php endif; ?>

    <?php if ($user): ?>
        <div>
            <div>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($user->getNombre()); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user->getEmail()); ?></p>
                <p><strong>Rol:</strong> <?php echo htmlspecialchars($user->getRol()); ?></p>
                
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
                
                <div>
                    <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/user/edit_profile.php">Editar Perfil</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div>Usuario no encontrado.</div>
    <?php endif; ?>
</div>

<?php include('../templates/footer.php'); ?>
