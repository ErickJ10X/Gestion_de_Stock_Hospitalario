<?php
session_start();
require_once(__DIR__ . '/../../controller/HospitalController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\HospitalController;
use util\Session;
use util\AuthGuard;

$hospitalController = new HospitalController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requireHospitalGestor();

$hospitales = $hospitalController->getAllHospitales();

$pageTitle = "Lista de Hospitales";
include_once(__DIR__ . '/../templates/header.php');
?>

<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/card-form.css">

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h2>Lista de Hospitales</h2>
            <button id="btn-add-hospital" class="btn btn-success">Nuevo Hospital</button>
        </div>
        <div class="card-body">
            <?php if ($session->hasMessage()): ?>
                <div class="alert alert-<?= $session->getMessageType() ?> alert-dismissible fade show" role="alert">
                    <?= $session->getMessage() ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($hospitales)): ?>
                        <tr>
                            <td colspan="3" class="text-center">No hay hospitales registrados</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($hospitales as $hospital): ?>
                            <tr>
                                <td><?= htmlspecialchars($hospital->id) ?></td>
                                <td><?= htmlspecialchars($hospital->nombre) ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm btn-edit-hospital" data-id="<?= $hospital->id ?>">
                                        <i class="bi bi-pencil-square"></i> Modificar
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-delete-hospital" data-id="<?= $hospital->id ?>">
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

<div class="hospital-overlay"></div>

<div id="hospital-card-create" class="hospital-card">
    <div class="hospital-card__header hospital-card__header--create">
        <h3 class="hospital-card__title">Nuevo Hospital</h3>
        <button type="button" class="hospital-card__close">&times;</button>
    </div>
    <div class="hospital-card__body">
        <form id="create-hospital-form" method="POST" action="registrar_hospital.php">
            <div class="hospital-form__group">
                <label for="nombre-create" class="hospital-form__label">Nombre del Hospital</label>
                <input type="text" class="hospital-form__input" id="nombre-create" name="nombre" required>
            </div>
            <div class="hospital-card__footer">
                <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel">Cancelar</button>
                <button type="submit" class="hospital-form__button hospital-form__button--primary">Registrar Hospital</button>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($hospitales)): ?>
    <?php foreach ($hospitales as $hospital): ?>
        <div id="hospital-card-edit-<?= $hospital->id ?>" class="hospital-card">
            <div class="hospital-card__header hospital-card__header--edit">
                <h3 class="hospital-card__title">Editar Hospital</h3>
                <button type="button" class="hospital-card__close">&times;</button>
            </div>
            <div class="hospital-card__body">
                <form id="edit-hospital-form-<?= $hospital->id ?>" method="POST" action="editar_hospital.php?id=<?= $hospital->id ?>">
                    <div class="hospital-form__group">
                        <label for="nombre-edit-<?= $hospital->id ?>" class="hospital-form__label">Nombre del Hospital</label>
                        <input type="text" class="hospital-form__input" id="nombre-edit-<?= $hospital->id ?>" name="nombre" value="<?= htmlspecialchars($hospital->nombre) ?>" required>
                    </div>
                    <div class="hospital-card__footer">
                        <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel">Cancelar</button>
                        <button type="submit" class="hospital-form__button hospital-form__button--primary">Actualizar Hospital</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="hospital-card-delete-<?= $hospital->id ?>" class="hospital-card">
            <div class="hospital-card__header hospital-card__header--delete">
                <h3 class="hospital-card__title">Eliminar Hospital</h3>
                <button type="button" class="hospital-card__close">&times;</button>
            </div>
            <div class="hospital-card__body">
                <h4>¿Estás seguro de que deseas eliminar el hospital "<?= htmlspecialchars($hospital->nombre) ?>"?</h4>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
                <form id="delete-hospital-form-<?= $hospital->id ?>" method="POST" action="eliminar_hospital.php?id=<?= $hospital->id ?>">
                    <input type="hidden" name="confirmar" value="1">
                    <div class="hospital-card__footer">
                        <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel">Cancelar</button>
                        <button type="submit" class="hospital-form__button hospital-form__button--danger">Confirmar Eliminación</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/hospital-cards.js"></script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
