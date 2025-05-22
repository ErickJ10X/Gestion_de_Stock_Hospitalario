<?php
session_start();
require_once __DIR__ . '/../../controller/AlmacenesController.php';
require_once __DIR__ . '/../../util/Session.php';
require_once __DIR__ . '/../../util/AuthGuard.php';
require_once __DIR__ . '/../../model/service/PlantaService.php';

use controller\AlmacenesController;
use util\Session;
use util\AuthGuard;

$authGuard = new AuthGuard();
$authGuard->requireHospitalGestor();

$almacenesController = new AlmacenesController();
$almacenesWithPlantas = $almacenesController->getAlmacenesWithPlantas();
$plantas = $almacenesController->getPlantas();

$session = new Session();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Almacenes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/card-form.css">
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
                <h2 class="mb-3">Gestión de Almacenes</h2>
            </div>
            <div class="col text-end">
                <button id="btn-add-almacen" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nuevo Almacén
                </button>
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
                <h3 class="card-title mb-0">Listado de Almacenes</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Planta</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($almacenesWithPlantas)): ?>
                                <tr>
                                    <td colspan="3" class="text-center">No hay almacenes registrados</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($almacenesWithPlantas as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['almacen']->getId()); ?></td>
                                        <td><?php echo $item['planta'] ? htmlspecialchars($item['planta']->getNombre()) : 'N/A'; ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm btn-action btn-edit-almacen" data-id="<?php echo $item['almacen']->getId(); ?>">
                                                <i class="bi bi-pencil"></i> Editar
                                            </button>
                                            <button class="btn btn-danger btn-sm btn-action btn-delete-almacen" data-id="<?php echo $item['almacen']->getId(); ?>">
                                                <i class="bi bi-trash"></i> Eliminar
                                            </button>
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

    <div class="almacen-overlay"></div>

    <div id="almacen-card-create" class="almacen-card">
        <div class="almacen-card__header almacen-card__header--create">
            <h3 class="almacen-card__title">Nuevo Almacén</h3>
            <button type="button" class="almacen-card__close">&times;</button>
        </div>
        <div class="almacen-card__body">
            <form id="create-almacen-form" method="POST" action="procesar_almacen.php">
                <input type="hidden" name="action" value="create">
                <div class="almacen-form__group">
                    <label for="planta_id-create" class="almacen-form__label">Planta</label>
                    <select class="almacen-form__select" id="planta_id-create" name="planta_id" required>
                        <option value="">Seleccionar planta</option>
                        <?php foreach ($plantas as $planta): ?>
                            <option value="<?php echo $planta->getId(); ?>">
                                <?php echo htmlspecialchars($planta->getNombre()); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="almacen-card__footer">
                    <button type="button" class="almacen-form__button almacen-form__button--secondary almacen-form__button--cancel">Cancelar</button>
                    <button type="submit" class="almacen-form__button almacen-form__button--primary">Registrar Almacén</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($almacenesWithPlantas)): ?>
        <?php foreach ($almacenesWithPlantas as $item): ?>
            <div id="almacen-card-edit-<?php echo $item['almacen']->getId(); ?>" class="almacen-card">
                <div class="almacen-card__header almacen-card__header--edit">
                    <h3 class="almacen-card__title">Editar Almacén</h3>
                    <button type="button" class="almacen-card__close">&times;</button>
                </div>
                <div class="almacen-card__body">
                    <form id="edit-almacen-form-<?php echo $item['almacen']->getId(); ?>" method="POST" action="procesar_almacen.php">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $item['almacen']->getId(); ?>">
                        <div class="almacen-form__group">
                            <label for="planta_id-edit-<?php echo $item['almacen']->getId(); ?>" class="almacen-form__label">Planta</label>
                            <select class="almacen-form__select" id="planta_id-edit-<?php echo $item['almacen']->getId(); ?>" name="planta_id" required>
                                <option value="">Seleccionar planta</option>
                                <?php foreach ($plantas as $planta): ?>
                                    <option value="<?php echo $planta->getId(); ?>" <?php echo ($item['almacen']->getPlantaId() == $planta->getId()) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($planta->getNombre()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="almacen-card__footer">
                            <button type="button" class="almacen-form__button almacen-form__button--secondary almacen-form__button--cancel">Cancelar</button>
                            <button type="submit" class="almacen-form__button almacen-form__button--primary">Actualizar Almacén</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="almacen-card-delete-<?php echo $item['almacen']->getId(); ?>" class="almacen-card">
                <div class="almacen-card__header almacen-card__header--delete">
                    <h3 class="almacen-card__title">Eliminar Almacén</h3>
                    <button type="button" class="almacen-card__close">&times;</button>
                </div>
                <div class="almacen-card__body">
                    <h4>¿Estás seguro de que deseas eliminar el almacén #<?php echo htmlspecialchars($item['almacen']->getId()); ?>?</h4>
                    <p class="text-danger">Esta acción no se puede deshacer.</p>
                    <form id="delete-almacen-form-<?php echo $item['almacen']->getId(); ?>" method="POST" action="procesar_almacen.php">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $item['almacen']->getId(); ?>">
                        <div class="almacen-card__footer">
                            <button type="button" class="almacen-form__button almacen-form__button--secondary almacen-form__button--cancel">Cancelar</button>
                            <button type="submit" class="almacen-form__button almacen-form__button--danger">Confirmar Eliminación</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php include(__DIR__ . '/../templates/footer.php'); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/almacen-cards.js"></script>
</body>
</html>
