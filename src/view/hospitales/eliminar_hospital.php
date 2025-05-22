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

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $session->setMessage('error', 'ID de hospital no válido');
    header('Location: lista_hospitales.php');
    exit;
}

$id = $_GET['id'];
$hospital = $hospitalController->getHospitalById($id);

if (!$hospital) {
    $session->setMessage('error', 'Hospital no encontrado');
    header('Location: lista_hospitales.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {
    if ($hospitalController->deleteHospital($id)) {
        $session->setMessage('success', 'Hospital eliminado correctamente');
    }
    header('Location: lista_hospitales.php');
    exit;
}

$pageTitle = "Eliminar Hospital";
include_once(__DIR__ . '/../templates/header.php');
?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h2>Eliminar Hospital</h2>
        </div>
        <div class="card-body">
            <h4>¿Estás seguro de que deseas eliminar el hospital "<?= htmlspecialchars($hospital->nombre) ?>"?</h4>
            <p class="text-danger">Esta acción no se puede deshacer.</p>
            
            <form method="POST">
                <div class="d-flex justify-content-between mt-4">
                    <a href="lista_hospitales.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" name="confirmar" value="1" class="btn btn-danger">Confirmar Eliminación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
