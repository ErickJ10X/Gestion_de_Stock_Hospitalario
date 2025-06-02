<?php
if(!isset($reposicionesController) || !isset($almacenes) || !isset($productos) || !isset($botiquines)) {
    die("Error: Variables no definidas. Asegúrese de que el controlador y los datos necesarios estén correctamente inicializados.");
}
$reposiciones = $reposicionesController->index()['reposiciones'] ?? [];
?>

<div class="tab-header">
    <h2>Órdenes Pendientes de Reposición</h2>
    <div class="tab-actions">
        <button type="button" class="btn btn-primary" id="nueva-reposicion">
            <i class="fas fa-plus"></i> Nueva Orden de Reposición
        </button>
    </div>
</div>

<div class="filter-container">
    <div class="filter-group">
        <label for="filtro-urgencia">Urgencia:</label>
        <select id="filtro-urgencia" class="form-control">
            <option value="">Todos</option>
            <option value="1">Urgentes</option>
            <option value="0">No urgentes</option>
        </select>
    </div>
    <div class="filter-group">
        <label for="filtro-almacen">Almacén origen:</label>
        <select id="filtro-almacen" class="form-control">
            <option value="">Todos los almacenes</option>
            <?php foreach ($almacenes as $almacen): ?>
                <option value="<?= $almacen->getIdAlmacen() ?>"><?= $almacen->getTipo() ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-group">
        <label for="filtro-botiquin">Botiquín destino:</label>
        <select id="filtro-botiquin" class="form-control">
            <option value="">Todos los botiquines</option>
            <?php foreach ($botiquines as $botiquin): ?>
                <option value="<?= $botiquin->getIdBotiquines() ?>"><?= $botiquin->getNombre() ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-group">
        <label for="filtro-producto">Producto:</label>
        <select id="filtro-producto" class="form-control">
            <option value="">Todos los productos</option>
            <?php foreach ($productos as $producto): ?>
                <option value="<?= $producto->getIdProducto() ?>"><?= $producto->getNombre() ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="button" class="btn btn-secondary" id="aplicar-filtros">Aplicar filtros</button>
    <button type="button" class="btn btn-outline-secondary" id="limpiar-filtros">Limpiar filtros</button>
</div>

<div class="table-responsive">
    <table class="list-table" id="tabla-reposiciones">
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Almacén Origen</th>
                <th>Botiquín Destino</th>
                <th>Cantidad</th>
                <th>Fecha</th>
                <th>Urgente</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reposiciones)): ?>
                <tr>
                    <td colspan="8" class="text-center">No hay órdenes pendientes</td>
                </tr>
            <?php else: ?>
                <?php foreach ($reposiciones as $reposicion): ?>
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
                    ?>
                    <tr data-id="<?= $reposicion->getIdReposicion() ?>" 
                        data-urgente="<?= $reposicion->getUrgente() ? '1' : '0' ?>"
                        data-almacen="<?= $reposicion->getDesdeAlmacen() ?>"
                        data-botiquin="<?= $reposicion->getHastaBotiquin() ?>"
                        data-producto="<?= $reposicion->getIdProducto() ?>">
                        <td><?= $reposicion->getIdReposicion() ?></td>
                        <td><?= $producto ? $producto->getNombre() : 'N/A' ?></td>
                        <td><?= $almacenOrigen ? $almacenOrigen->getTipo() : 'N/A' ?></td>
                        <td><?= $botiquinDestino ? $botiquinDestino->getNombre() : 'N/A' ?></td>
                        <td><?= $reposicion->getCantidadRepuesta() ?></td>
                        <td><?= $reposicion->getFecha() ?></td>
                        <td>
                            <span class="badge <?= $reposicion->getUrgente() ? 'badge-danger' : 'badge-secondary' ?>">
                                <?= $reposicion->getUrgente() ? 'Urgente' : 'Normal' ?>
                            </span>
                        </td>
                        <td class="actions">
                            <button type="button" class="btn-icon btn-edit" data-id="<?= $reposicion->getIdReposicion() ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn-icon btn-delete" data-id="<?= $reposicion->getIdReposicion() ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button type="button" class="btn-icon btn-complete" data-id="<?= $reposicion->getIdReposicion() ?>">
                                <i class="fas fa-check-circle"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Formulario para crear/editar reposiciones -->
<div class="card-form" id="form-reposicion" style="display: none;">
    <div class="card-form__header">
        <h3 id="form-title">Nueva Orden de Reposición</h3>
        <button type="button" class="card-form__close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <form id="reposicion-form" method="post">
        <input type="hidden" id="id_reposicion" name="id_reposicion">
        
        <div class="form-group">
            <label for="id_producto">Producto *</label>
            <select id="id_producto" name="id_producto" class="form-control" required>
                <option value="">Seleccione un producto</option>
                <?php foreach ($productos as $producto): ?>
                    <option value="<?= $producto->getIdProducto() ?>">
                        <?= $producto->getCodigo() ?> - <?= $producto->getNombre() ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="desde_almacen">Desde Almacén *</label>
            <select id="desde_almacen" name="desde_almacen" class="form-control" required>
                <option value="">Seleccione un almacén</option>
                <?php foreach ($almacenes as $almacen): ?>
                    <option value="<?= $almacen->getIdAlmacen() ?>">
                        <?= $almacen->getTipo() ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="hasta_botiquin">Hasta Botiquín *</label>
            <select id="hasta_botiquin" name="hasta_botiquin" class="form-control" required>
                <option value="">Seleccione un botiquín</option>
                <?php foreach ($botiquines as $botiquin): ?>
                    <option value="<?= $botiquin->getIdBotiquines() ?>">
                        <?= $botiquin->getNombre() ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="cantidad_repuesta">Cantidad *</label>
            <input type="number" id="cantidad_repuesta" name="cantidad_repuesta" class="form-control" min="1" required>
        </div>
        
        <div class="form-group">
            <label for="fecha">Fecha</label>
            <input type="datetime-local" id="fecha" name="fecha" class="form-control">
            <small class="form-text text-muted">Si se deja en blanco, se usará la fecha actual.</small>
        </div>
        
        <div class="form-group form-check">
            <input type="checkbox" id="urgente" name="urgente" class="form-check-input" value="1">
            <label for="urgente" class="form-check-label">Urgente</label>
        </div>
        
        <div class="form-buttons">
            <button type="submit" class="btn btn-primary" id="guardar-reposicion">Guardar</button>
            <button type="button" class="btn btn-secondary cancel-form">Cancelar</button>
        </div>
    </form>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal-confirm" id="confirm-delete" style="display: none;">
    <div class="modal-confirm__content">
        <div class="modal-confirm__header">
            <h3>Confirmar eliminación</h3>
            <button type="button" class="modal-confirm__close">&times;</button>
        </div>
        <div class="modal-confirm__body">
            <p>¿Está seguro de que desea eliminar esta orden de reposición?</p>
            <p>Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-confirm__footer">
            <button type="button" class="btn btn-danger" id="btn-confirmar-eliminar">Eliminar</button>
            <button type="button" class="btn btn-secondary modal-confirm__close">Cancelar</button>
        </div>
    </div>
</div>

<!-- El código JavaScript se ha movido al archivo reposiciones.js -->
