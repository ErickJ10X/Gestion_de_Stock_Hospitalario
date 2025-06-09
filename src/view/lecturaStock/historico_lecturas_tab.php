<?php
if (!isset($botiquines) || !isset($lecturas) || !isset($detallesLecturas)) {
    echo '<p>Error: Datos necesarios no disponibles.</p>';
    exit;
}
?>

<!-- Tarjeta principal de la tabla -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="search-group">
            <div class="input-group input-group-sm">
                <label for="filtro-botiquin" class="input-group-text">Filtrar por botiquín:</label>
                <select id="filtro-botiquin" class="form-select form-select-sm">
                    <option value="">Todos los botiquines</option>
                    <?php foreach ($botiquines as $botiquin): ?>
                        <option value="<?= $botiquin->getIdBotiquin() ?>">
                            <?= htmlspecialchars($botiquin->getNombre()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="button" id="btn-filtrar" class="btn btn-outline-secondary">
                    <i class="fas fa-filter me-1"></i> Filtrar
                </button>
                <button type="button" id="btn-reset-filtros" class="btn btn-outline-danger">
                    <i class="fas fa-times me-1"></i> Limpiar
                </button>
            </div>

            <div class="input-group input-group-sm">
                <select id="registrosPorPaginaLecturas" class="form-select form-select-sm">
                    <option value="5">5 registros</option>
                    <option value="10" selected>10 registros</option>
                    <option value="25">25 registros</option>
                    <option value="50">50 registros</option>
                </select>
            </div>

            <div class="input-group input-group-sm">
                <input type="text" id="buscarLectura" class="form-control" placeholder="Buscar...">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="tabla-lecturas" class="list-table table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Botiquín</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Fecha de lectura</th>
                        <th>Registrado por</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($lecturas)): ?>
                        <?php foreach ($lecturas as $lectura): ?>
                            <?php
                                // Intentar obtener el botiquín y producto de las relaciones
                                $botiquin = $lectura->getBotiquin();
                                $producto = $lectura->getProducto();

                                $nombreBotiquin = $botiquin ? $botiquin->getNombre() : 'Desconocido';
                                $nombreProducto = $producto ? $producto->getCodigo() . ' - ' . $producto->getNombre() : 'Desconocido';
                            ?>
                            <tr class="list-table__body-row" data-botiquin="<?= $lectura->getIdBotiquin() ?>">
                                <td class="list-table__body-cell" data-label="ID"><?= $lectura->getIdLectura() ?></td>
                                <td class="list-table__body-cell" data-label="Botiquín"><?= htmlspecialchars($nombreBotiquin) ?></td>
                                <td class="list-table__body-cell" data-label="Producto"><?= htmlspecialchars($nombreProducto) ?></td>
                                <td class="list-table__body-cell" data-label="Cantidad"><?= $lectura->getCantidadDisponible() ?></td>
                                <td class="list-table__body-cell" data-label="Fecha"><?= $lectura->getFechaLectura()->format('d/m/Y H:i') ?></td>
                                <td class="list-table__body-cell" data-label="Usuario"><?= $lectura->getRegistradoPor() ?></td>
                                <td class="list-table__body-cell" data-label="Acciones">
                                    <div class="list-table__actions">
                                        <button class="list-table__button list-table__button--edit btn-view-lectura"
                                                onclick="verDetalleLectura(<?= $lectura->getIdLectura() ?>)">
                                            <i class="bi bi-eye list-table__button-icon"></i> Ver
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="list-table__empty">No hay lecturas registradas</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación con JS -->
        <div class="card-footer bg-white py-3">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <div class="paginacion-info">
                        Mostrando <span id="inicio-registros-lecturas">1</span> a <span id="fin-registros-lecturas">10</span>
                        de <span id="total-registros-lecturas"><?= count($lecturas) ?></span> registros
                    </div>
                </div>
                <div class="col-md-7">
                    <nav aria-label="Paginación de lecturas">
                        <ul class="pagination justify-content-end mb-0" id="paginacion-lecturas">
                            <!-- La paginación se generará dinámicamente con JavaScript -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalles de lectura -->
<div id="modal-detalle-lectura" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Lectura de Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detalle-lectura-content">
                    <!-- El contenido se cargará dinámicamente desde JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Inicializar datos de lecturas para paginación
    window.datosLecturas = <?= json_encode(array_map(function ($l) {
        $botiquin = $l->getBotiquin();
        $producto = $l->getProducto();
        return [
            'id' => $l->getIdLectura(),
            'botiquin_id' => $l->getIdBotiquin(),
            'botiquin_nombre' => $botiquin ? $botiquin->getNombre() : 'Desconocido',
            'producto_nombre' => $producto ? $producto->getCodigo() . ' - ' . $producto->getNombre() : 'Desconocido',
            'cantidad' => $l->getCantidadDisponible(),
            'fecha' => $l->getFechaLectura()->format('d/m/Y H:i'),
            'usuario' => $l->getRegistradoPor()
        ];
    }, $lecturas)) ?>;
</script>
