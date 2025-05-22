<?php
session_start();
require_once __DIR__ . '/../../controller/AlmacenesController.php';
require_once __DIR__ . '/../../util/Session.php';
require_once __DIR__ . '/../../util/AuthGuard.php';

use controller\AlmacenesController;
use util\Session;
use util\AuthGuard;

$authGuard = new AuthGuard();
$authGuard->checkSession();

$almacenesController = new AlmacenesController();
$plantas = $almacenesController->getPlantas();

$session = new Session();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Almacén</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</head>
<body>
    <?php include(__DIR__ . '/../templates/header.php'); ?>

    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col">
                <h2>Crear Nuevo Almacén</h2>
            </div>
            <div class="col text-end">
                <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/tabla_almacenes.php" class="btn btn-secondary">
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
                <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/procesar_almacen.php" method="post">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-3">
                        <label for="planta_id" class="form-label">Planta</label>
                        <select class="form-select" id="planta_id" name="planta_id" required>
                            <option value="">Seleccionar planta</option>
                            <?php foreach ($plantas as $planta): ?>
                                <option value="<?php echo $planta->getId(); ?>">
                                    <?php echo htmlspecialchars($planta->getNombre()); ?>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
