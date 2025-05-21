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

$authGuard->checkSession();

// Verificar que se ha proporcionado un ID de hospital
if (!isset($_GET['hospital_id']) || empty($_GET['hospital_id'])) {
    $session->setMessage('error', 'ID de hospital no proporcionado');
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
    exit;
}

$hospitalId = $_GET['hospital_id'];
$hospital = $hospitalController->getHospitalById($hospitalId);

// Si no se encuentra el hospital, redirigir
if (!$hospital) {
    $session->setMessage('error', 'Hospital no encontrado');
    header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php');
    exit;
}

try {
    $plantas = $plantaController->getPlantasByHospitalId($hospitalId);
} catch (Exception $e) {
    $plantas = [];
    $session->setMessage('error', 'Error al cargar las plantas: ' . $e->getMessage());
}

$pageTitle = "Plantas del Hospital: " . $hospital->getNombre();
include_once(__DIR__ . '/../templates/header.php');
?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col">
            <h2>Plantas del Hospital: <?php echo htmlspecialchars($hospital->getNombre()); ?></h2>
        </div>
        <div class="col text-end">
            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/lista_hospitales.php" class="btn btn-secondary me-2">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/registrar_planta.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nueva Planta
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
            <h3 class="card-title mb-0">Listado de Plantas</h3>
        </div>
        <div class="card-body">
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
                        <?php if (empty($plantas)): ?>
                            <tr>
                                <td colspan="3" class="text-center">No hay plantas registradas para este hospital</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($plantas as $planta): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($planta->getId()); ?></td>
                                    <td><?php echo htmlspecialchars($planta->getNombre()); ?></td>
                                    <td>
                                        <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/editar_planta.php?id=<?php echo $planta->getId(); ?>" class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $planta->getId(); ?>">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>

                                        <!-- Modal de confirmación para eliminar -->
                                        <div class="modal fade" id="deleteModal<?php echo $planta->getId(); ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $planta->getId(); ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title" id="deleteModalLabel<?php echo $planta->getId(); ?>">Confirmar eliminación</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ¿Está seguro de que desea eliminar la planta <strong><?php echo htmlspecialchars($planta->getNombre()); ?></strong>?
                                                        <p class="text-danger mt-3">Esta acción no se puede deshacer.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/eliminar_planta.php" method="post">
                                                            <input type="hidden" name="id" value="<?php echo $planta->getId(); ?>">
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
