<?php
session_start();
require_once(__DIR__ . '/../../controller/HospitalController.php');
require_once(__DIR__ . '/../../controller/PlantaController.php');
require_once(__DIR__ . '/../../controller/BotiquinController.php');
include_once(__DIR__ . '/../../util/Session.php');
include_once(__DIR__ . '/../../util/AuthGuard.php');

use controller\HospitalController;
use controller\PlantaController;
use controller\BotiquinController;
use util\Session;
use util\AuthGuard;

$hospitalController = new HospitalController();
$plantaController = new PlantaController();
$botiquinController = new BotiquinController();
$session = new Session();
$authGuard = new AuthGuard();

// Verificar que el usuario sea dashboard o gestor general
$authGuard->requireAdministrador();

// Obtener todos los hospitales
$responseHospitales = $hospitalController->index();
$hospitales = $responseHospitales['error'] ? [] : $responseHospitales['hospitales'];

// Obtener los datos de todas las plantas y botiquines para tenerlos disponibles
$responsePlantas = $plantaController->index();
$plantas = $responsePlantas['error'] ? [] : $responsePlantas['plantas'];

$responseBotiquines = $botiquinController->index();
$botiquines = $responseBotiquines['error'] ? [] : $responseBotiquines['botiquines'];

// Organizar plantas por hospital
$plantasPorHospital = [];
foreach ($plantas as $planta) {
    if (!isset($plantasPorHospital[$planta->id_hospital])) {
        $plantasPorHospital[$planta->id_hospital] = [];
    }
    $plantasPorHospital[$planta->id_hospital][] = $planta;
}

// Organizar botiquines por planta
$botiquinesPorPlanta = [];
foreach ($botiquines as $botiquin) {
    if (!isset($botiquinesPorPlanta[$botiquin->id_planta])) {
        $botiquinesPorPlanta[$botiquin->id_planta] = [];
    }
    $botiquinesPorPlanta[$botiquin->id_planta][] = $botiquin;
}

$pageTitle = "Panel de Administración - Vista General";
include_once(__DIR__ . '/../templates/header.php');
?>

<div class="container-fluid mt-4">
    <h1 class="mb-4">Vista General del Sistema</h1>
    
    <?php if ($session->hasMessage('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $session->getMessage('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($session->hasMessage('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $session->getMessage('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Total Hospitales</h5>
                    <h2 class="display-4"><?= count($hospitales) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Total Plantas</h5>
                    <h2 class="display-4"><?= count($plantas) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Total Botiquines</h5>
                    <h2 class="display-4"><?= count($botiquines) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Estructura Jerárquica de Hospitales, Plantas y Botiquines
        </div>
        <div class="card-body">
            <div class="accordion" id="hospitalAccordion">
                <?php if (empty($hospitales)): ?>
                    <div class="alert alert-info">No hay hospitales registrados en el sistema.</div>
                <?php else: ?>
                    <?php foreach ($hospitales as $index => $hospital): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-hospital-<?= $hospital->id ?>">
                                <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-hospital-<?= $hospital->id ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="collapse-hospital-<?= $hospital->id ?>">
                                    <i class="fas fa-hospital me-2"></i> <strong><?= htmlspecialchars($hospital->nombre) ?></strong>
                                </button>
                            </h2>
                            <div id="collapse-hospital-<?= $hospital->id ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="heading-hospital-<?= $hospital->id ?>" data-bs-parent="#hospitalAccordion">
                                <div class="accordion-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <h5>Plantas de <?= htmlspecialchars($hospital->nombre) ?></h5>
                                        <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/registrar_planta.php?id_hospital=<?= $hospital->id ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-plus"></i> Añadir Planta
                                        </a>
                                    </div>
                                    
                                    <?php if (!isset($plantasPorHospital[$hospital->id]) || empty($plantasPorHospital[$hospital->id])): ?>
                                        <div class="alert alert-warning">Este hospital no tiene plantas registradas.</div>
                                    <?php else: ?>
                                        <div class="accordion" id="plantaAccordion-<?= $hospital->id ?>">
                                            <?php foreach ($plantasPorHospital[$hospital->id] as $planta): ?>
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-planta-<?= $planta->id ?>">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-planta-<?= $planta->id ?>" aria-expanded="false" aria-controls="collapse-planta-<?= $planta->id ?>">
                                                            <i class="fas fa-building me-2"></i> <strong><?= htmlspecialchars($planta->nombre) ?></strong>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse-planta-<?= $planta->id ?>" class="accordion-collapse collapse" aria-labelledby="heading-planta-<?= $planta->id ?>" data-bs-parent="#plantaAccordion-<?= $hospital->id ?>">
                                                        <div class="accordion-body">
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <h6>Botiquines de <?= htmlspecialchars($planta->nombre) ?></h6>
                                                                <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/registrar_botiquin.php?id_planta=<?= $planta->id ?>" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-plus"></i> Añadir Botiquín
                                                                </a>
                                                            </div>
                                                            
                                                            <?php if (!isset($botiquinesPorPlanta[$planta->id]) || empty($botiquinesPorPlanta[$planta->id])): ?>
                                                                <div class="alert alert-warning">Esta planta no tiene botiquines registrados.</div>
                                                            <?php else: ?>
                                                                <div class="table-responsive">
                                                                    <table class="table table-striped table-hover">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Código</th>
                                                                                <th>Ubicación</th>
                                                                                <th>Tipo</th>
                                                                                <th>Estado</th>
                                                                                <th>Acciones</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php foreach ($botiquinesPorPlanta[$planta->id] as $botiquin): ?>
                                                                                <tr>
                                                                                    <td><?= htmlspecialchars($botiquin->codigo) ?></td>
                                                                                    <td><?= htmlspecialchars($botiquin->ubicacion) ?></td>
                                                                                    <td><?= htmlspecialchars($botiquin->tipo) ?></td>
                                                                                    <td>
                                                                                        <span class="badge bg-<?= $botiquin->activo ? 'success' : 'danger' ?>">
                                                                                            <?= $botiquin->activo ? 'Activo' : 'Inactivo' ?>
                                                                                        </span>
                                                                                    </td>
                                                                                    <td>
                                                                                        <div class="btn-group btn-group-sm">
                                                                                            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/detalle_botiquin.php?id=<?= $botiquin->id ?>" class="btn btn-primary">
                                                                                                <i class="fas fa-eye"></i>
                                                                                            </a>
                                                                                            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/editar_botiquin.php?id=<?= $botiquin->id ?>" class="btn btn-warning">
                                                                                                <i class="fas fa-edit"></i>
                                                                                            </a>
                                                                                            <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/medicamentos/lista_medicamentos.php?id_botiquin=<?= $botiquin->id ?>" class="btn btn-info">
                                                                                                <i class="fas fa-pills"></i>
                                                                                            </a>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php endforeach; ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            <?php endif; ?>
                                                            
                                                            <div class="mt-2">
                                                                <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/plantas/editar_planta.php?id=<?= $planta->id ?>" class="btn btn-warning btn-sm">
                                                                    <i class="fas fa-edit"></i> Editar Planta
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="mt-3">
                                        <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/editar_hospital.php?id=<?= $hospital->id ?>" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Editar Hospital
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Distribución de Botiquines por Hospital
                </div>
                <div class="card-body">
                    <canvas id="hospitalChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js para gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener datos para el gráfico
    const hospitalesData = <?= json_encode(array_map(function($h) { return $h->nombre; }, $hospitales)) ?>;
    
    // Contar botiquines por hospital
    const botiquinesPorHospital = [];
    
    <?php foreach ($hospitales as $hospital): ?>
        let count = 0;
        <?php if (isset($plantasPorHospital[$hospital->id])): ?>
            <?php foreach ($plantasPorHospital[$hospital->id] as $planta): ?>
                <?php if (isset($botiquinesPorPlanta[$planta->id])): ?>
                    count += <?= count($botiquinesPorPlanta[$planta->id]) ?>;
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        botiquinesPorHospital.push(count);
    <?php endforeach; ?>
    
    // Crear gráfico
    const ctx = document.getElementById('hospitalChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: hospitalesData,
            datasets: [{
                label: 'Número de Botiquines',
                data: botiquinesPorHospital,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cantidad de Botiquines'
                    },
                    ticks: {
                        precision: 0
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Hospitales'
                    }
                }
            }
        }
    });
});
</script>

<?php include_once(__DIR__ . '/../templates/footer.php'); ?>
