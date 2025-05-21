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

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h2>Lista de Hospitales</h2>
            <a href="registrar_hospital.php" class="btn btn-success">Nuevo Hospital</a>
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
                                    <a href="editar_hospital.php?id=<?= $hospital->id ?>" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-square"></i> Modificar
                                    </a>
                                    <a href="eliminar_hospital.php?id=<?= $hospital->id ?>" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </a>
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

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
