<?php

use controller\HospitalController;
use controller\PlantaController;
use util\AuthGuard;
use util\Session;

session_start();
require_once(__DIR__ . '/../../controller/PlantaController.php');
require_once(__DIR__ . '/../../controller/HospitalController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

$plantaController = new PlantaController();
$hospitalController = new HospitalController();
$session = new Session();
$authGuard = new AuthGuard();

$authGuard->requirePlantaGestor();

try {
    $hospitales = $hospitalController->getAllHospitales();
} catch (Exception $e) {
    $hospitales = [];
    $session->setMessage('error', 'Error al cargar los hospitales: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre']) && isset($_POST['hospital_id'])) {
    $nombre = trim($_POST['nombre']);
    $hospitalId = $_POST['hospital_id'];
    
    if (empty($nombre)) {
        $session->setMessage('error', 'El nombre de la planta es obligatorio');
    } elseif (empty($hospitalId)) {
        $session->setMessage('error', 'Debe seleccionar un hospital');
    } else {
        if ($plantaController->createPlanta($nombre, $hospitalId)) {
            $session->setMessage('success', 'Plantas registrada correctamente');
            header('Location: /Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/listar_plantas.php');
            exit;
        }
    }
}

$pageTitle = "Registrar Plantas";
include_once(__DIR__ . '/../templates/header.php');
?>

<div class="container mt-4">
    <div class="row mb-3">
        <div class="col">
            <h2>Registrar Nueva Planta</h2>
        </div>
        <div class="col text-end">
            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/listar_plantas.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <?php if ($session->hasMessage('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $session->getMessage('error'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">Formulario de Registro</h3>
        </div>
        <div class="card-body">
            <form action="" method="post">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre de la Planta</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                
                <div class="mb-3">
                    <label for="hospital_id" class="form-label">Hospital</label>
                    <select class="form-select" id="hospital_id" name="hospital_id" required>
                        <option value="">Seleccionar hospital</option>
                        <?php foreach ($hospitales as $hospital): ?>
                            <option value="<?php echo $hospital->getId(); ?>">
                                <?php echo htmlspecialchars($hospital->getNombre()); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary me-md-2">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include(__DIR__ . '/../templates/footer.php'); ?>
