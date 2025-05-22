<?php
session_start();
require_once(__DIR__ . '/../../controller/PlantaController.php');
require_once(__DIR__ . '/../../controller/HospitalController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

$plantaController = new \controller\PlantaController();
$hospitalController = new \controller\HospitalController();
$session = new \util\Session();
$authGuard = new \util\AuthGuard();

$authGuard->requirePlantaGestor();

try {
    $plantas = $plantaController->getAllPlantas();
    $hospitalesMap = [];
    $hospitales = $hospitalController->getAllHospitales();
    
    // Crear un mapa de hospitales para mostrar nombres en lugar de IDs
    foreach ($hospitales as $hospital) {
        $hospitalesMap[$hospital->getId()] = $hospital->getNombre();
    }
} catch (Exception $e) {
    $plantas = [];
    $hospitales = [];
    $session->setMessage('error', 'Error al cargar los datos: ' . $e->getMessage());
}

$pageTitle = "Listado de Plantas";
include_once(__DIR__ . '/../templates/header.php');
?>

<!-- Incluir el archivo CSS para las tarjetas -->
<link rel="stylesheet" href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/css/card-form.css">

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col">
            <h2>Gestión de Plantas</h2>
        </div>
        <div class="col text-end">
            <button id="btn-add-planta" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nueva Planta
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
            <h3 class="card-title mb-0">Listado de Plantas</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Hospital</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($plantas)): ?>
                            <tr>
                                <td colspan="4" class="text-center">No hay plantas registradas</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($plantas as $planta): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($planta->getId()); ?></td>
                                    <td><?php echo htmlspecialchars($planta->getNombre()); ?></td>
                                    <td>
                                        <?php 
                                        $hospitalId = $planta->getHospitalId();
                                        echo isset($hospitalesMap[$hospitalId]) ? htmlspecialchars($hospitalesMap[$hospitalId]) : 'Desconocido';
                                        ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-warning btn-sm btn-edit-planta" data-id="<?php echo $planta->getId(); ?>">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                        <button class="btn btn-danger btn-sm btn-delete-planta" data-id="<?php echo $planta->getId(); ?>">
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

<!-- Overlay para el fondo oscurecido -->
<div class="planta-overlay"></div>

<!-- Tarjeta flotante para crear una nueva planta -->
<div id="planta-card-create" class="planta-card">
    <div class="planta-card__header planta-card__header--create">
        <h3 class="planta-card__title">Nueva Planta</h3>
        <button type="button" class="planta-card__close">&times;</button>
    </div>
    <div class="planta-card__body">
        <form id="create-planta-form" method="POST" action="procesar_planta.php">
            <input type="hidden" name="action" value="create">
            <div class="planta-form__group">
                <label for="nombre-create" class="planta-form__label">Nombre de la Planta</label>
                <input type="text" class="planta-form__input" id="nombre-create" name="nombre" required>
            </div>
            <div class="planta-form__group">
                <label for="hospital_id-create" class="planta-form__label">Hospital</label>
                <select class="planta-form__select" id="hospital_id-create" name="hospital_id" required>
                    <option value="">Seleccionar hospital</option>
                    <?php foreach ($hospitales as $hospital): ?>
                        <option value="<?php echo $hospital->getId(); ?>">
                            <?php echo htmlspecialchars($hospital->getNombre()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="planta-card__footer">
                <button type="button" class="planta-form__button planta-form__button--secondary planta-form__button--cancel">Cancelar</button>
                <button type="submit" class="planta-form__button planta-form__button--primary">Registrar Planta</button>
            </div>
        </form>
    </div>
</div>

<!-- Tarjetas flotantes para editar y eliminar plantas -->
<?php if (!empty($plantas)): ?>
    <?php foreach ($plantas as $planta): ?>
        <div id="planta-card-edit-<?php echo $planta->getId(); ?>" class="planta-card">
            <div class="planta-card__header planta-card__header--edit">
                <h3 class="planta-card__title">Editar Planta</h3>
                <button type="button" class="planta-card__close">&times;</button>
            </div>
            <div class="planta-card__body">
                <form id="edit-planta-form-<?php echo $planta->getId(); ?>" method="POST" action="procesar_planta.php">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo $planta->getId(); ?>">
                    <div class="planta-form__group">
                        <label for="nombre-edit-<?php echo $planta->getId(); ?>" class="planta-form__label">Nombre de la Planta</label>
                        <input type="text" class="planta-form__input" id="nombre-edit-<?php echo $planta->getId(); ?>" name="nombre" value="<?php echo htmlspecialchars($planta->getNombre()); ?>" required>
                    </div>
                    <div class="planta-form__group">
                        <label for="hospital_id-edit-<?php echo $planta->getId(); ?>" class="planta-form__label">Hospital</label>
                        <select class="planta-form__select" id="hospital_id-edit-<?php echo $planta->getId(); ?>" name="hospital_id" required>
                            <option value="">Seleccionar hospital</option>
                            <?php foreach ($hospitales as $hospital): ?>
                                <option value="<?php echo $hospital->getId(); ?>" <?php echo ($planta->getHospitalId() == $hospital->getId()) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($hospital->getNombre()); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="planta-card__footer">
                        <button type="button" class="planta-form__button planta-form__button--secondary planta-form__button--cancel">Cancelar</button>
                        <button type="submit" class="planta-form__button planta-form__button--primary">Actualizar Planta</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="planta-card-delete-<?php echo $planta->getId(); ?>" class="planta-card">
            <div class="planta-card__header planta-card__header--delete">
                <h3 class="planta-card__title">Eliminar Planta</h3>
                <button type="button" class="planta-card__close">&times;</button>
            </div>
            <div class="planta-card__body">
                <h4>¿Estás seguro de que deseas eliminar la planta "<?php echo htmlspecialchars($planta->getNombre()); ?>"?</h4>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
                <form id="delete-planta-form-<?php echo $planta->getId(); ?>" method="POST" action="procesar_planta.php">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo $planta->getId(); ?>">
                    <div class="planta-card__footer">
                        <button type="button" class="planta-form__button planta-form__button--secondary planta-form__button--cancel">Cancelar</button>
                        <button type="submit" class="planta-form__button planta-form__button--danger">Confirmar Eliminación</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include(__DIR__ . '/../templates/footer.php'); ?>

<!-- Scripts después del footer -->
<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/planta-cards.js"></script>
