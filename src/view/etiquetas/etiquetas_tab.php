<?php
if (!isset($etiquetas) || !isset($productosInfo)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<!-- Tarjeta principal de la tabla -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <a href="#" class="btn btn-sm btn-success" onclick="activarPestania('tab-generar'); return false;">
            <i class="fas fa-plus-circle me-1"></i> Nueva Etiqueta
        </a>

        <div class="search-group">
            <div class="input-group input-group-sm">
                <select id="registrosPorPaginaEtiquetas" class="form-select form-select-sm">
                    <option value="5">5 registros</option>
                    <option value="10" selected>10 registros</option>
                    <option value="25">25 registros</option>
                    <option value="50">50 registros</option>
                </select>
            </div>
            <div class="input-group input-group-sm">
                <input type="text" id="buscarEtiqueta" class="form-control" placeholder="Buscar...">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="list-table table table-striped table-hover" id="etiquetasDataTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Reposición</th>
                        <th>Tipo</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($etiquetas)): ?>
                        <tr>
                            <td colspan="7" class="list-table__empty">No hay etiquetas registradas</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($etiquetas as $etiqueta): ?>
                            <tr class="list-table__body-row">
                                <td class="list-table__body-cell" data-label="ID"><?= $etiqueta->getIdEtiqueta(); ?></td>
                                <td class="list-table__body-cell" data-label="Producto">
                                    <?php if (isset($productosInfo[$etiqueta->getIdProducto()])): ?>
                                        <?= htmlspecialchars($productosInfo[$etiqueta->getIdProducto()]->getNombre()); ?>
                                        <br>
                                        <small class="text-muted">
                                            <?php 
                                                $producto = $productosInfo[$etiqueta->getIdProducto()];
                                                if (method_exists($producto, 'getReferencia')) {
                                                    echo htmlspecialchars($producto->getReferencia() ?: '');
                                                } elseif (method_exists($producto, 'getCodigo')) {
                                                    echo htmlspecialchars($producto->getCodigo() ?: '');
                                                }
                                            ?>
                                        </small>
                                    <?php else: ?>
                                        ID: <?= $etiqueta->getIdProducto(); ?>
                                    <?php endif; ?>
                                </td>
                                <td class="list-table__body-cell" data-label="Reposición"><?= $etiqueta->getIdReposicion(); ?></td>
                                <td class="list-table__body-cell" data-label="Tipo">
                                    <?php if ($etiqueta->getTipo() === 'RFID'): ?>
                                        <span class="badge bg-info">RFID</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Informativa</span>
                                    <?php endif; ?>
                                </td>
                                <td class="list-table__body-cell" data-label="Prioridad">
                                    <?php if ($etiqueta->getPrioridad() === 'Urgente'): ?>
                                        <span class="badge bg-danger">Urgente</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">Normal</span>
                                    <?php endif; ?>
                                </td>
                                <td class="list-table__body-cell" data-label="Estado">
                                    <?php if ($etiqueta->isImpresa()): ?>
                                        <span class="badge bg-success">Impresa</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                                <td class="list-table__body-cell" data-label="Acciones">
                                    <div class="list-table__actions">
                                        <button class="list-table__button list-table__button--view btn-view-etiqueta"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#verEtiquetaModal"
                                                data-id="<?= $etiqueta->getIdEtiqueta(); ?>"
                                                data-tipo="<?= $etiqueta->getTipo(); ?>"
                                                data-prioridad="<?= $etiqueta->getPrioridad(); ?>"
                                                data-producto="<?= isset($productosInfo[$etiqueta->getIdProducto()]) ? htmlspecialchars($productosInfo[$etiqueta->getIdProducto()]->getNombre()) : ''; ?>"
                                                data-referencia="<?php 
                                                    if (isset($productosInfo[$etiqueta->getIdProducto()])) {
                                                        $producto = $productosInfo[$etiqueta->getIdProducto()];
                                                        if (method_exists($producto, 'getReferencia')) {
                                                            echo htmlspecialchars($producto->getReferencia() ?: '');
                                                        } elseif (method_exists($producto, 'getCodigo')) {
                                                            echo htmlspecialchars($producto->getCodigo() ?: '');
                                                        }
                                                    }
                                                ?>"
                                                data-reposicion="<?= $etiqueta->getIdReposicion(); ?>">
                                            <i class="bi bi-eye list-table__button-icon"></i> Ver
                                        </button>
                                        
                                        <?php if (!$etiqueta->isImpresa()): ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="id_etiqueta" value="<?= $etiqueta->getIdEtiqueta(); ?>">
                                                <input type="hidden" name="accion" value="imprimir">
                                                <button type="submit" class="list-table__button list-table__button--print">
                                                    <i class="bi bi-printer list-table__button-icon"></i> Imprimir
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <form method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta etiqueta?')">
                                            <input type="hidden" name="id_etiqueta" value="<?= $etiqueta->getIdEtiqueta(); ?>">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <button type="submit" class="list-table__button list-table__button--delete">
                                                <i class="bi bi-trash list-table__button-icon"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="card-footer bg-white py-3">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <div class="paginacion-info">
                        Mostrando <span id="inicio-registros-etiquetas">1</span> a <span id="fin-registros-etiquetas">10</span>
                        de <span id="total-registros-etiquetas"><?= count($etiquetas) ?></span> registros
                    </div>
                </div>
                <div class="col-md-7">
                    <nav aria-label="Paginación de etiquetas">
                        <ul class="pagination justify-content-end mb-0" id="paginacion-etiquetas">
                            <!-- La paginación se generará dinámicamente con JavaScript -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver etiqueta -->
<div class="modal fade" id="verEtiquetaModal" tabindex="-1" aria-labelledby="verEtiquetaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verEtiquetaModalLabel">
                    <i class="fas fa-tag me-2"></i> Vista Previa de Etiqueta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="preview-container">
                    <div id="modal-etiqueta-preview" class="etiqueta-preview normal modal-etiqueta-preview">
                        <div class="preview-header">
                            <div class="color-indicator blue" id="modal-color-indicator"></div>
                            <span id="modal-tipo-prioridad">Normal Informativa</span>
                        </div>
                        <div class="preview-body">
                            <p><strong>Producto:</strong> <span id="modal-producto">-</span></p>
                            <p><strong>Referencia:</strong> <span id="modal-referencia">-</span></p>
                            <p><strong>Reposición ID:</strong> <span id="modal-reposicion">-</span></p>
                            <p><strong>Fecha:</strong> <span id="modal-fecha"><?php echo date('d/m/Y'); ?></span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success btn-imprimir-modal" id="btn-imprimir-modal">
                    <i class="bi bi-printer"></i> Imprimir Etiqueta
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Inicializar datos de etiquetas para paginación
    window.datosEtiquetas = <?= json_encode(array_map(function ($e) use ($productosInfo) {
        $nombreProducto = isset($productosInfo[$e->getIdProducto()]) ? $productosInfo[$e->getIdProducto()]->getNombre() : 'ID: ' . $e->getIdProducto();
        
        // Obtener referencia del producto
        $referencia = '';
        if (isset($productosInfo[$e->getIdProducto()])) {
            $producto = $productosInfo[$e->getIdProducto()];
            if (method_exists($producto, 'getReferencia')) {
                $referencia = $producto->getReferencia() ?: '';
            } elseif (method_exists($producto, 'getCodigo')) {
                $referencia = $producto->getCodigo() ?: '';
            }
        }
        
        return [
            'id' => $e->getIdEtiqueta(),
            'producto' => $nombreProducto,
            'referencia' => $referencia,
            'reposicion' => $e->getIdReposicion(),
            'tipo' => $e->getTipo(),
            'prioridad' => $e->getPrioridad(),
            'impresa' => $e->isImpresa()
        ];
    }, $etiquetas)) ?>;
</script>
