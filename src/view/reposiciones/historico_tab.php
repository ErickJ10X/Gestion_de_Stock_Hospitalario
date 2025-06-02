<?php
// Esta pestaña muestra el histórico de reposiciones realizadas

if(!isset($reposicionesController) || !isset($almacenes) || !isset($productos) || !isset($botiquines)) {
    die('No se puede acceder directamente a este archivo.');
}
// Obtener todas las reposiciones y ordenarlas por fecha (más reciente primero)
$historialReposiciones = $reposicionesController->index()['reposiciones'] ?? [];
usort($historialReposiciones, function($a, $b) {
    return strtotime($b->getFecha()) - strtotime($a->getFecha());
});

// Extraer años y meses para los filtros
$listaAnios = [];
$listaMeses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',
    4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
    7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
    10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];

// Extraer años únicos de las fechas de reposiciones
foreach ($historialReposiciones as $reposicion) {
    $fecha = new DateTime($reposicion->getFecha());
    $anio = $fecha->format('Y');
    if (!in_array($anio, $listaAnios)) {
        $listaAnios[] = $anio;
    }
}
// Si no hay años, agregar el año actual
if (empty($listaAnios)) {
    $listaAnios[] = date('Y');
}
rsort($listaAnios); // Ordenar años de más reciente a más antiguo
?>

<div class="tab-header">
    <h2>Histórico de Reposiciones</h2>
    <div class="tab-actions">
        <button type="button" class="btn btn-outline-primary" id="btn-exportar-historico">
            <i class="fas fa-file-export"></i> Exportar a Excel
        </button>
    </div>
</div>

<div class="filter-container">
    <div class="filter-group">
        <label for="filtro-anio">Año:</label>
        <select id="filtro-anio" class="form-control">
            <option value="">Todos</option>
            <?php foreach ($listaAnios as $anio): ?>
                <option value="<?= $anio ?>"><?= $anio ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-group">
        <label for="filtro-mes">Mes:</label>
        <select id="filtro-mes" class="form-control">
            <option value="">Todos</option>
            <?php foreach ($listaMeses as $num => $mes): ?>
                <option value="<?= $num ?>"><?= $mes ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-group">
        <label for="filtro-producto-historico">Producto:</label>
        <select id="filtro-producto-historico" class="form-control">
            <option value="">Todos los productos</option>
            <?php foreach ($productos as $producto): ?>
                <option value="<?= $producto->getIdProducto() ?>"><?= $producto->getNombre() ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-group">
        <label for="filtro-almacen-historico">Almacén origen:</label>
        <select id="filtro-almacen-historico" class="form-control">
            <option value="">Todos los almacenes</option>
            <?php foreach ($almacenes as $almacen): ?>
                <option value="<?= $almacen->getIdAlmacen() ?>"><?= $almacen->getTipo() ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-group">
        <label for="filtro-botiquin-historico">Botiquín destino:</label>
        <select id="filtro-botiquin-historico" class="form-control">
            <option value="">Todos los botiquines</option>
            <?php foreach ($botiquines as $botiquin): ?>
                <option value="<?= $botiquin->getIdBotiquines() ?>"><?= $botiquin->getNombre() ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="button" class="btn btn-secondary" id="aplicar-filtros-historico">Aplicar filtros</button>
    <button type="button" class="btn btn-outline-secondary" id="limpiar-filtros-historico">Limpiar filtros</button>
</div>

<div class="table-responsive">
    <table class="list-table" id="tabla-historico">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Almacén Origen</th>
                <th>Botiquín Destino</th>
                <th>Cantidad</th>
                <th>Urgencia</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($historialReposiciones)): ?>
                <tr>
                    <td colspan="8" class="text-center">No hay registros históricos disponibles</td>
                </tr>
            <?php else: ?>
                <?php foreach ($historialReposiciones as $reposicion): ?>
                    <?php
                    // Obtener datos relacionados
                    $producto = null;
                    foreach ($productos as $p) {
                        if ($p->getIdProducto() == $reposicion->getIdProducto()) {
                            $producto = $p;
                            break;
                        }
                    }

                    $almacenOrigen = null;
                    foreach ($almacenes as $a) {
                        if ($a->getIdAlmacen() == $reposicion->getDesdeAlmacen()) {
                            $almacenOrigen = $a;
                            break;
                        }
                    }

                    $botiquinDestino = null;
                    foreach ($botiquines as $b) {
                        if ($b->getIdBotiquines() == $reposicion->getHastaBotiquin()) {
                            $botiquinDestino = $b;
                            break;
                        }
                    }

                    // Formatear fecha para filtros y visualización
                    $fechaObj = new DateTime($reposicion->getFecha());
                    $fechaFormateada = $fechaObj->format('d/m/Y H:i');
                    $anio = $fechaObj->format('Y');
                    $mes = $fechaObj->format('n');
                    ?>
                    <tr
                        data-id="<?= $reposicion->getIdReposicion() ?>"
                        data-producto="<?= $reposicion->getIdProducto() ?>"
                        data-almacen="<?= $reposicion->getDesdeAlmacen() ?>"
                        data-botiquin="<?= $reposicion->getHastaBotiquin() ?>"
                        data-anio="<?= $anio ?>"
                        data-mes="<?= $mes ?>"
                    >
                        <td><?= $reposicion->getIdReposicion() ?></td>
                        <td><?= $fechaFormateada ?></td>
                        <td><?= $producto ? $producto->getNombre() : 'N/A' ?></td>
                        <td><?= $almacenOrigen ? $almacenOrigen->getTipo() : 'N/A' ?></td>
                        <td><?= $botiquinDestino ? $botiquinDestino->getNombre() : 'N/A' ?></td>
                        <td>
                            <?= $reposicion->getCantidadRepuesta() ?>
                            <?= $producto ? $producto->getUnidadMedida() : '' ?>
                        </td>
                        <td>
                            <span class="badge <?= $reposicion->getUrgente() ? 'badge-danger' : 'badge-secondary' ?>">
                                <?= $reposicion->getUrgente() ? 'Urgente' : 'Normal' ?>
                            </span>
                        </td>
                        <td class="actions">
                            <button type="button" class="btn-icon btn-view" data-id="<?= $reposicion->getIdReposicion() ?>" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn-icon btn-print" data-id="<?= $reposicion->getIdReposicion() ?>" title="Imprimir">
                                <i class="fas fa-print"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para ver detalles de reposición -->
<div class="modal-details" id="modal-detalles-reposicion" style="display: none;">
    <div class="modal-details__content">
        <div class="modal-details__header">
            <h3>Detalles de Reposición</h3>
            <button type="button" class="modal-details__close">&times;</button>
        </div>
        <div class="modal-details__body">
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">ID:</span>
                    <span class="detail-value" id="detalle-id"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Fecha:</span>
                    <span class="detail-value" id="detalle-fecha"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Producto:</span>
                    <span class="detail-value" id="detalle-producto"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Código Producto:</span>
                    <span class="detail-value" id="detalle-codigo-producto"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Almacén Origen:</span>
                    <span class="detail-value" id="detalle-almacen"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Botiquín Destino:</span>
                    <span class="detail-value" id="detalle-botiquin"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Cantidad:</span>
                    <span class="detail-value" id="detalle-cantidad"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Urgencia:</span>
                    <span class="detail-value" id="detalle-urgencia"></span>
                </div>
            </div>
        </div>
        <div class="modal-details__footer">
            <button type="button" class="btn btn-primary" id="btn-imprimir-detalle">Imprimir</button>
            <button type="button" class="btn btn-secondary modal-details__close">Cerrar</button>
        </div>
    </div>
</div>

<!-- El código JavaScript se ha movido al archivo reposiciones.js -->

<style>
/* Estilos para el histórico */
.modal-details {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-details__content {
    background: white;
    width: 600px;
    max-width: 90%;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.modal-details__header {
    background: #f5f5f5;
    padding: 15px 20px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-details__header h3 {
    margin: 0;
    font-size: 18px;
}

.modal-details__close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #888;
}

.modal-details__body {
    padding: 20px;
}

.details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.detail-item {
    padding: 8px;
    border-bottom: 1px solid #eee;
}

.detail-label {
    font-weight: 600;
    color: #555;
    display: block;
    margin-bottom: 5px;
}

.detail-value {
    display: block;
}

.text-danger {
    color: #dc3545;
    font-weight: 600;
}

.text-secondary {
    color: #6c757d;
} /* Faltaba esta llave de cierre */

.modal-details__footer {
    background: #f5f5f5;
    padding: 15px 20px;
    border-top: 1px solid #ddd;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* Ajustes para badges */
.badge {
    display: inline-block;
    padding: 0.25em 0.6em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
}

.badge-danger {
    color: #fff;
    background-color: #dc3545;
}

.badge-secondary {
    color: #fff;
    background-color: #6c757d;
}
</style>
