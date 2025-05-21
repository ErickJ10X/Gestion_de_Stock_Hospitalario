<?php
session_start();
require_once __DIR__ . '/../../controller/BotiquinesController.php';
require_once __DIR__ . '/../../util/Session.php';
require_once __DIR__ . '/../../util/AuthGuard.php';
require_once __DIR__ . '/../../model/service/PlantaService.php';

use controller\BotiquinesController;
use util\Session;
use util\AuthGuard;

$authGuard = new AuthGuard();
$authGuard->checkSession();

$botiquinesController = new BotiquinesController();
$botiquinesWithPlantas = $botiquinesController->getBotiquinesWithPlantas();

$session = new Session();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Botiquines</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        .table-responsive {
            margin-top: 20px;
        }
        .btn-action {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/../templates/header.php'); ?>

    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col">
                <h2 class="mb-3">Gestión de Botiquines</h2>
            </div>
            <div class="col text-end">
                <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/crear_botiquin.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nuevo Botiquín
                </a>
            </div>
        </div>

        <?php if ($session->hasMessage('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $session->getMessage('success'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($session->hasMessage('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $session->getMessage('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0">Listado de Botiquines</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Planta</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($botiquinesWithPlantas)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">No hay botiquines registrados</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($botiquinesWithPlantas as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['botiquin']->getId()); ?></td>
                                        <td><?php echo htmlspecialchars($item['botiquin']->getNombre()); ?></td>
                                        <td><?php echo $item['planta'] ? htmlspecialchars($item['planta']->getNombre()) : 'N/A'; ?></td>
                                        <td>
                                            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/editar_botiquin.php?id=<?php echo $item['botiquin']->getId(); ?>" class="btn btn-warning btn-sm btn-action">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm btn-action" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $item['botiquin']->getId(); ?>">
                                                <i class="bi bi-trash"></i> Eliminar
                                            </button>

                                            <!-- Modal de confirmación para eliminar -->
                                            <div class="modal fade" id="deleteModal<?php echo $item['botiquin']->getId(); ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $item['botiquin']->getId(); ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title" id="deleteModalLabel<?php echo $item['botiquin']->getId(); ?>">Confirmar eliminación</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            ¿Está seguro de que desea eliminar el botiquín <strong><?php echo htmlspecialchars($item['botiquin']->getNombre()); ?></strong>?
                                                            <p class="text-danger mt-3">Esta acción no se puede deshacer.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/procesar_botiquin.php" method="post">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id" value="<?php echo $item['botiquin']->getId(); ?>">
                                                                <button type="submit" class="btn btn-danger">Eliminar</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include(__DIR__ . '/../templates/footer.php'); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
