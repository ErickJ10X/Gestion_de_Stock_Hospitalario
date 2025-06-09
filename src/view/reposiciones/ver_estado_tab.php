<?php
if(!isset($reposicionesController) || !isset($almacenes) || !isset($productos) || !isset($botiquines)) {
    die('No se puede acceder directamente a este archivo.');
}

// Determinar si debemos filtrar por estado
$filtroEstado = null;
if (isset($_GET['estado'])) {
    $filtroEstado = ($_GET['estado'] === '1') ? true :
        (($_GET['estado'] === '0') ? false : null);
}

// Si hay un filtro de estado aplicado, cargar solo reposiciones con ese estado
if ($filtroEstado !== null) {
    $datosReposiciones = $reposicionesController->getReposicionesPorEstado($filtroEstado);
    $reposiciones = $datosReposiciones['reposiciones'] ?? [];
}
?>

<div class="tab-header">
    <h2>Estado de Reposiciones</h2>
    <div class="tab-actions">
        <div class="filtros-container">
            <div class="filtro-group">
                <label for="filtro-estado">Estado:</label>
                <select id="filtro-estado" class="form-select" onchange="cambiarFiltroEstado(this.value)">
                    <option value="todos" <?= !isset($_GET['estado']) ? 'selected' : '' ?>>Todos</option>
                    <option value="0" <?= (isset($_GET['estado']) && $_GET['estado'] === '0') ? 'selected' : '' ?>>Pendientes</option>
                    <option value="1" <?= (isset($_GET['estado']) && $_GET['estado'] === '1') ? 'selected' : '' ?>>Entregados</option>
                </select>
            </div>
            <div class="filtro-group">
                <label for="filtro-urgencia">Urgencia:</label>
                <select id="filtro-urgencia" class="form-select">
                    <option value="todos">Todos</option>
                    <option value="urgente">Solo Urgentes</option>
                    <option value="normal">Solo Normales</option>
                </select>
            </div>
            <div class="filtro-group">
                <label for="filtro-botiquin">Botiquín:</label>
                <select id="filtro-botiquin" class="form-select">
                    <option value="todos">Todos los botiquines</option>
                    <?php foreach ($botiquines as $botiquin): ?>
                        <option value="<?= $botiquin->getIdBotiquin() ?>"><?= htmlspecialchars($botiquin->getNombre()) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filtro-group">
                <button id="aplicar-filtros" class="btn btn-primary">Aplicar Filtros</button>
                <button id="limpiar-filtros" class="btn btn-secondary">Limpiar Filtros</button>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="list-table" id="tablaReposiciones">
        <thead>
        <tr>
            <th>ID</th>
            <th>Producto</th>
            <th>Desde Almacén</th>
            <th>Hasta Botiquín</th>
            <th>Cantidad</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Urgencia</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($reposiciones)): ?>
            <tr>
                <td colspan="9" class="list-table__empty">No hay reposiciones registradas</td>
            </tr>
        <?php else: ?>
            <?php foreach ($reposiciones as $reposicion):
                // Buscar nombre del producto
                $nombreProducto = "Producto no encontrado";
                foreach ($productos as $producto) {
                    if ($producto->getIdProducto() == $reposicion->getIdProducto()) {
                        $nombreProducto = $producto->getNombre();
                        break;
                    }
                }

                // Buscar nombre del almacén
                $nombreAlmacen = "Almacén no encontrado";
                foreach ($almacenes as $almacen) {
                    if ($almacen->getIdAlmacen() == $reposicion->getDesdeAlmacen()) {
                        $nombreAlmacen = $almacen->getTipo();
                        break;
                    }
                }

                // Buscar nombre del botiquín
                $nombreBotiquin = "Botiquín no encontrado";
                foreach ($botiquines as $botiquin) {
                    if ($botiquin->getIdBotiquin() == $reposicion->getHaciaBotiquin()) {
                        $nombreBotiquin = $botiquin->getNombre();
                        break;
                    }
                }

                // Determinar el estado utilizando el nuevo campo
                $estado = $reposicion->getEstado() ? "Entregado" : "Pendiente";
                $estadoClass = $reposicion->getEstado() ? "badge bg-success" : "badge bg-warning";

                // Determinar la clase de urgencia
                $urgenciaClass = $reposicion->isUrgente() ? "badge bg-danger" : "badge bg-primary";
                $urgenciaText = $reposicion->isUrgente() ? "URGENTE" : "Normal";
                ?>
                <tr class="list-table__body-row" data-estado="<?= $reposicion->getEstado() ? '1' : '0' ?>"
                    data-urgente="<?= $reposicion->isUrgente() ? 'urgente' : 'normal' ?>"
                    data-botiquin="<?= $reposicion->getHaciaBotiquin() ?>">
                    <td class="list-table__body-cell"><?= $reposicion->getId() ?></td>
                    <td class="list-table__body-cell"><?= htmlspecialchars($nombreProducto) ?></td>
                    <td class="list-table__body-cell"><?= htmlspecialchars($nombreAlmacen) ?></td>
                    <td class="list-table__body-cell"><?= htmlspecialchars($nombreBotiquin) ?></td>
                    <td class="list-table__body-cell"><?= $reposicion->getCantidadRepuesta() ?></td>
                    <td class="list-table__body-cell"><?= $reposicion->getFecha()->format('d/m/Y H:i') ?></td>
                    <td class="list-table__body-cell">
                        <span class="<?= $estadoClass ?>"><?= $estado ?></span>
                    </td>
                    <td class="list-table__body-cell">
                        <span class="<?= $urgenciaClass ?>"><?= $urgenciaText ?></span>
                    </td>
                    <td class="list-table__body-cell list-table__actions">
                        <?php if (!$reposicion->getEstado()): // Mostrar botones de acción solo para pendientes ?>
                            <form method="post" class="d-inline" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/ReposicionesController.php">
                                <input type="hidden" name="action" value="marcar_entregada">
                                <input type="hidden" name="id_reposicion" value="<?= $reposicion->getId() ?>">
                                <button type="submit" class="btn btn-sm btn-success" title="Marcar como entregada">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                        <button class="btn btn-sm btn-info ver-detalles" title="Ver detalles" data-id="<?= $reposicion->getId() ?>">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // Función para cambiar el filtro de estado y recargar la página
    function cambiarFiltroEstado(valor) {
        const url = new URL(window.location.href);

        if (valor === 'todos') {
            url.searchParams.delete('estado');
        } else {
            url.searchParams.set('estado', valor);
        }

        window.location.href = url.toString();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Filtros adicionales (urgencia y botiquín) mediante JavaScript
        const aplicarFiltros = document.getElementById('aplicar-filtros');
        const limpiarFiltros = document.getElementById('limpiar-filtros');
        const filtroUrgencia = document.getElementById('filtro-urgencia');
        const filtroBotiquin = document.getElementById('filtro-botiquin');

        if (aplicarFiltros) {
            aplicarFiltros.addEventListener('click', function() {
                const filas = document.querySelectorAll('#tablaReposiciones tbody tr');
                let hayResultados = false;

                filas.forEach(function(fila) {
                    // Saltar la fila de "No hay reposiciones"
                    if (fila.querySelector('.list-table__empty')) return;

                    let mostrar = true;

                    // Filtrar por urgencia
                    if (filtroUrgencia.value !== 'todos') {
                        if (fila.dataset.urgente !== filtroUrgencia.value) {
                            mostrar = false;
                        }
                    }

                    // Filtrar por botiquín
                    if (filtroBotiquin.value !== 'todos') {
                        if (fila.dataset.botiquin !== filtroBotiquin.value) {
                            mostrar = false;
                        }
                    }

                    fila.style.display = mostrar ? '' : 'none';
                    if (mostrar) hayResultados = true;
                });

                // Mostrar mensaje si no hay resultados
                const tbody = document.querySelector('#tablaReposiciones tbody');
                const existingNoResults = document.querySelector('.no-results-row');

                if (!hayResultados && filas.length > 0) {
                    if (!existingNoResults) {
                        const noResultsRow = document.createElement('tr');
                        noResultsRow.className = 'no-results-row';
                        noResultsRow.innerHTML = '<td colspan="9" class="text-center">No se encontraron resultados con los filtros seleccionados</td>';
                        tbody.appendChild(noResultsRow);
                    } else {
                        existingNoResults.style.display = '';
                    }
                } else if (existingNoResults) {
                    existingNoResults.style.display = 'none';
                }
            });
        }

        if (limpiarFiltros) {
            limpiarFiltros.addEventListener('click', function() {
                // Restaurar los selectores a sus valores por defecto
                filtroUrgencia.value = 'todos';
                filtroBotiquin.value = 'todos';

                // Mostrar todas las filas excepto mensajes de error
                const filas = document.querySelectorAll('#tablaReposiciones tbody tr:not(.no-results-row)');
                filas.forEach(function(fila) {
                    fila.style.display = '';
                });

                // Ocultar mensaje de no resultados si existe
                const noResultsRow = document.querySelector('.no-results-row');
                if (noResultsRow) {
                    noResultsRow.style.display = 'none';
                }
            });
        }
    });
</script>

<style>
    .tab-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .tab-header h2 {
        margin: 0;
    }

    .filtros-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }

    .filtro-group {
        display: flex;
        flex-direction: column;
        min-width: 150px;
    }

    .filtro-group label {
        margin-bottom: 5px;
        font-weight: 500;
    }

    .list-table__actions {
        white-space: nowrap;
        min-width: 100px;
    }

    .list-table__actions .btn {
        margin-right: 5px;
    }
</style>