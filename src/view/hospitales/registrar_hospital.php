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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    $nombre = trim($_POST['nombre']);
    
    if (empty($nombre)) {
        $session->setMessage('error', 'El nombre del hospital es obligatorio');
    } else {
        $resultado = $hospitalController->store($nombre);
        
        if (!$resultado['error']) {
            $session->setMessage('success', $resultado['mensaje']);
            header('Location: lista_hospitales.php');
            exit;
        } else {
            $session->setMessage('error', $resultado['mensaje']);
        }
    }
}

$pageTitle = "Registrar Hospital";
include_once(__DIR__ . '/../templates/header.php');
?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h2>Registrar Nuevo Hospital</h2>
        </div>
        <div class="card-body">
            <?php if ($session->hasMessage()): ?>
                <div class="alert alert-<?= $session->getMessageType() ?> alert-dismissible fade show" role="alert">
                    <?= $session->getMessage() ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php $session->clearMessage(); ?>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Hospital</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="lista_hospitales.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Registrar Hospital</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
