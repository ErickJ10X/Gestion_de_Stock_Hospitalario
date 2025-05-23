<?php
session_start();
require_once __DIR__ . '/../../controller/BotiquinesController.php';
require_once __DIR__ . '/../../util/Session.php';
require_once __DIR__ . '/../../util/AuthGuard.php';
require_once __DIR__ . '/../../util/Redirect.php';

use controller\BotiquinesController;
use util\Session;
use util\AuthGuard;
use util\Redirect;

$authGuard = new AuthGuard();
$authGuard->requireAuth();

$session = new Session();
$botiquinesController = new BotiquinesController();

// Validar que se ha proporcionado un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $session->setMessage('error', 'ID de botiquín no proporcionado');
    Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/tabla_botiquines.php');
    exit;
}

$id = $_GET['id'];
$botiquin = $botiquinesController->getBotiquinById($id);

if (!$botiquin) {
    $session->setMessage('error', 'Botiquín no encontrado');
    Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/tabla_botiquines.php');
    exit;
}

$plantas = $botiquinesController->getPlantas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Botiquín</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</head>
<body>
    <?php include(__DIR__ . '/../templates/header.php'); ?>

    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col">
                <h2>Editar Botiquín</h2>
            </div>
            <div class="col text-end">
                <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/tabla_botiquines.php" class="btn btn-secondary">
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
            <div class="card-header bg-warning text-dark">
                <h3 class="card-title mb-0">Formulario de Edición</h3>
            </div>
            <div class="card-body">
                <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/procesar_botiquin.php" method="post">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo $botiquin->getIdBotiquines(); ?>">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Botiquín</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($botiquin->getNombre()); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="planta_id" class="form-label">Planta</label>
                        <select class="form-select" id="planta_id" name="planta_id" required>
                            <option value="">Seleccionar planta</option>
                            <?php foreach ($plantas as $planta): ?>
                                <option value="<?php echo $planta->getId(); ?>" <?php echo ($planta->getId() == $botiquin->getIdPlanta()) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($planta->getNombre()); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/tabla_botiquines.php" class="btn btn-secondary me-md-2">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save"></i> Actualizar
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
