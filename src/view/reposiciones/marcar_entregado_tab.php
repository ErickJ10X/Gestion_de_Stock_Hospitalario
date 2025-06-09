<?php
if(!isset($reposicionesController) || !isset($almacenes) || !isset($productos) || !isset($botiquines)) {
    die('No se puede acceder directamente a este archivo.');
}

// Obtener filtros
$idBotiquin = isset($_GET['botiquin']) ? (int)$_GET['botiquin'] : null;
$urgente = isset($_GET['urgente']) ? ($_GET['urgente'] === '1') : null;

// Cargar solo las reposiciones PENDIENTES (estado = false)
$resultadoPendientes = $reposicionesController->getReposicionesPendientes();
$reposicionesPendientes = $resultadoPendientes['success'] ? $resultadoPendientes['reposiciones'] : [];

// Para depuración
error_log("Total reposiciones pendientes encontradas: " . count($reposicionesPendientes));

// Filtrar los resultados por botiquín y urgencia si corresponde
if ($idBotiquin || $urgente !== null) {
    $filtradas = [];
    foreach ($reposicionesPendientes as $reposicion) {
        $cumpleFiltros = true;

        // Filtrar por botiquín
        if ($idBotiquin && $reposicion->getHaciaBotiquin() != $idBotiquin) {
            $cumpleFiltros = false;
        }

        // Filtrar por urgencia
        if ($urgente !== null && $reposicion->isUrgente() != $urgente) {
            $cumpleFiltros = false;
        }

        if ($cumpleFiltros) {
            $filtradas[] = $reposicion;
        }
    }
    $reposicionesPendientes = $filtradas;
}
?>

<div class="tab-header">
    <h2>Marcar Reposiciones como Entregadas</h2>
</div>

<div class="entrega-container">
    <div class="card shadow mb-4">
        <div class="card-header">
            <h3>Seleccione Reposiciones Pendientes</h3>
            <p class="text-muted mb-0">
                <i class="fas fa-info-circle"></i> Se muestran solo reposiciones pendientes (estado = no entregado)
            </p>
        </div>
        <div class="card-body">
            <!-- Filtros de búsqueda -->
            <div class="filtros-form mb-4">
                <form method="GET" action="" id="form-filtros">
                    <input type="hidden" name="tab" value="marcar-entregado">
                    <input type="hidden" name="filtro_aplicado" value="1">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="filtro-botiquin-entrega">Filtrar por botiquín:</label>
                                <select id="filtro-botiquin-entrega" name="botiquin" class="form-select">
                                    <option value="">Todos los botiquines</option>
                                    <?php foreach ($botiquines as $botiquin): ?>
                                        <option value="<?= $botiquin->getIdBotiquin() ?>" <?= ($idBotiquin === $botiquin->getIdBotiquin()) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($botiquin->getNombre()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="filtro-urgencia-entrega">Filtrar por urgencia:</label>
                                <select id="filtro-urgencia-entrega" name="urgente" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="1" <?= ($urgente === true) ? 'selected' : '' ?>>Solo Urgentes</option>
                                    <option value="0" <?= ($urgente === false) ? 'selected' : '' ?>>Solo Normales</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" id="buscar-pendientes" class="btn btn-primary">Buscar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabla de reposiciones pendientes -->
            <div class="table-responsive">
                <!-- Formulario para marcar como entregadas -->
                <form id="form-entregas" method="POST" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/ReposicionesController.php">
                    <input type="hidden" name="action" value="marcar_entregadas">

                    <table class="list-table" id="tablaPendientes">
                        <thead>
                        <tr>
                            <th>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="seleccionar-todas">
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Cantidad</th>
                            <th>Fecha</th>
                            <th>Urgencia</th>
                            <th>Estado</th>
                        </tr>
                        </thead>
                        <tbody id="pendientes-tbody">
                        <?php if (empty($reposicionesPendientes)): ?>
                            <tr>
                                <td colspan="9" class="text-center">No se encontraron reposiciones pendientes</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reposicionesPendientes as $reposicion):
                                // Verificar que la reposición esté pendiente (estado = false)
                                if ($reposicion->getEstado() === true) {
                                    error_log("Omitiendo reposición " . $reposicion->getId() . " porque ya está entregada");
                                    continue;
                                }

                                $id = $reposicion->getId();
                                error_log("Mostrando reposición pendiente ID: $id");

                                // Buscar información adicional
                                $nombreProducto = "Producto ID: " . $reposicion->getIdProducto();
                                foreach ($productos as $producto) {
                                    if ($producto->getIdProducto() == $reposicion->getIdProducto()) {
                                        $nombreProducto = $producto->getCodigo() . ' - ' . $producto->getNombre();
                                        break;
                                    }
                                }

                                $nombreAlmacen = "Almacén ID: " . $reposicion->getDesdeAlmacen();
                                foreach ($almacenes as $almacen) {
                                    if ($almacen->getIdAlmacen() == $reposicion->getDesdeAlmacen()) {
                                        $nombreAlmacen = $almacen->getTipo();
                                        break;
                                    }
                                }

                                $nombreBotiquin = "Botiquín ID: " . $reposicion->getHaciaBotiquin();
                                foreach ($botiquines as $botiquin) {
                                    if ($botiquin->getIdBotiquin() == $reposicion->getHaciaBotiquin()) {
                                        $nombreBotiquin = $botiquin->getNombre();
                                        break;
                                    }
                                }

                                $urgenciaClass = $reposicion->isUrgente() ? 'badge bg-danger' : 'badge bg-primary';
                                $urgenciaText = $reposicion->isUrgente() ? 'URGENTE' : 'Normal';
                                ?>
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input checkbox-reposicion" type="checkbox" name="ids_reposicion[]" value="<?= $id ?>">
                                        </div>
                                    </td>
                                    <td><?= $id ?></td>
                                    <td><?= htmlspecialchars($nombreProducto) ?></td>
                                    <td><?= htmlspecialchars($nombreAlmacen) ?></td>
                                    <td><?= htmlspecialchars($nombreBotiquin) ?></td>
                                    <td><?= $reposicion->getCantidadRepuesta() ?></td>
                                    <td><?= $reposicion->getFecha()->format('d/m/Y H:i') ?></td>
                                    <td><span class="<?= $urgenciaClass ?>"><?= $urgenciaText ?></span></td>
                                    <td><span class="badge bg-warning">Pendiente</span></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (count($reposicionesPendientes) === 0): ?>
                                <tr>
                                    <td colspan="9" class="text-center">No se encontraron reposiciones pendientes</td>
                                </tr>
                            <?php endif; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="mt-4 text-center" id="entrega-actions" style="display:<?= empty($reposicionesPendientes) ? 'none' : 'block' ?>;">
                        <div class="form-group mb-3">
                            <label for="fecha_entrega" class="form-label">Fecha y Hora de Entrega</label>
                            <input type="datetime-local" id="fecha_entrega" name="fecha_entrega" class="form-control form-control-inline" style="max-width: 300px;">
                            <small class="text-muted">Deje en blanco para usar la fecha actual</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="notas_entrega" class="form-label">Notas de Entrega</label>
                            <textarea id="notas_entrega" name="notas_entrega" class="form-control" style="max-width: 500px; margin: 0 auto;" rows="3" placeholder="Observaciones sobre la entrega (opcional)"></textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit" id="btn-confirmar-entrega" class="btn btn-success">
                                <i class="fas fa-check-circle"></i> Confirmar Entrega
                            </button>
                            <button type="reset" id="btn-cancelar-entrega" class="btn btn-secondary">
                                <i class="fas fa-times-circle"></i> Cancelar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectTodas = document.getElementById('seleccionar-todas');
        const checkboxesReposicion = document.querySelectorAll('.checkbox-reposicion');
        const formEntregas = document.getElementById('form-entregas');
        const entregaActions = document.getElementById('entrega-actions');

        // Manejar selección de todas las reposiciones
        if (selectTodas) {
            selectTodas.addEventListener('change', function() {
                checkboxesReposicion.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });

                // Mostrar u ocultar el panel de acciones según si hay items seleccionados
                actualizarVisibilidadAcciones();
            });
        }

        // Manejar cambios en checkboxes individuales
        checkboxesReposicion.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Verificar si todos están seleccionados para actualizar el checkbox "seleccionar todas"
                if (selectTodas) {
                    const totalCheckboxes = checkboxesReposicion.length;
                    const checkedCheckboxes = document.querySelectorAll('.checkbox-reposicion:checked').length;

                    selectTodas.checked = checkedCheckboxes === totalCheckboxes;
                    selectTodas.indeterminate = checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes;
                }

                // Actualizar visibilidad del panel de acciones
                actualizarVisibilidadAcciones();
            });
        });

        // Función para actualizar visibilidad del panel de acciones
        function actualizarVisibilidadAcciones() {
            if (entregaActions) {
                const haySeleccionados = document.querySelectorAll('.checkbox-reposicion:checked').length > 0;
                entregaActions.style.display = haySeleccionados ? 'block' : 'none';
            }
        }

        // Validación del formulario
        if (formEntregas) {
            formEntregas.addEventListener('submit', function(event) {
                const checkboxesSeleccionados = document.querySelectorAll('.checkbox-reposicion:checked');

                if (checkboxesSeleccionados.length === 0) {
                    alert('Debe seleccionar al menos una reposición para marcarla como entregada');
                    event.preventDefault();
                    return false;
                }

                // Verificar que el formulario tenga los campos necesarios
                if (!formEntregas.querySelector('input[name="action"]')) {
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'marcar_entregadas';
                    formEntregas.appendChild(actionInput);
                }

                // Mostrar los IDs que se van a enviar para depuración
                console.log("Enviando reposiciones para marcar como entregadas:",
                    Array.from(checkboxesSeleccionados).map(cb => cb.value).join(", ")
                );

                // Confirmar antes de enviar
                if (!confirm('¿Está seguro que desea marcar ' + checkboxesSeleccionados.length + ' reposición(es) como entregada(s)?')) {
                    event.preventDefault();
                    return false;
                }

                return true;
            });
        }

        // Manejar cancelación
        const btnCancelar = document.getElementById('btn-cancelar-entrega');
        if (btnCancelar) {
            btnCancelar.addEventListener('click', function() {
                // Deseleccionar todas las casillas
                if (selectTodas) {
                    selectTodas.checked = false;
                    selectTodas.indeterminate = false;
                }

                checkboxesReposicion.forEach(checkbox => {
                    checkbox.checked = false;
                });

                // Ocultar panel de acciones
                if (entregaActions) {
                    entregaActions.style.display = 'none';
                }
            });
        }

        // Inicializar estado de visibilidad del panel de acciones
        actualizarVisibilidadAcciones();
    });
</script>

<style>
    .entrega-container {
        margin-bottom: 30px;
    }

    .filtros-form {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
    }

    .form-control-inline {
        width: auto;
        display: inline-block;
        margin: 0 auto;
    }

    .card-header {
        background-color: #f8f9fa;
        padding: 15px 20px;
    }

    .card-header h3 {
        margin-bottom: 5px;
    }

    .text-muted {
        color: #6c757d !important;
    }

    .d-grid {
        display: grid;
    }
</style>