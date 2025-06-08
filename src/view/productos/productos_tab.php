<?php
if (!isset($productos) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<!-- Tarjeta principal de la tabla -->
<div class="card shadow mb-4">
    <div class="card-header py-3">

        <button id="btnNuevoProducto" class="btn btn-sm btn-success">
            <i class="fas fa-plus-circle me-1"></i> Nuevo Producto
        </button>

        <div class="search-group">
            <div class="input-group input-group-sm">
                <select id="registrosPorPaginaProductos" class="form-select form-select-sm">
                    <option value="5">5 registros</option>
                    <option value="10" selected>10 registros</option>
                    <option value="25">25 registros</option>
                    <option value="50">50 registros</option>
                </select>
            </div>
            <div class="input-group input-group-sm">
                <input type="text" id="buscarProducto" class="form-control" placeholder="Buscar...">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="list-table table table-striped table-hover" id="productosDataTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Unidad de Medida</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($productos)): ?>
                    <tr>
                        <td colspan="6" class="list-table__empty">No hay productos registrados</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($productos as $producto): ?>
                        <tr class="list-table__body-row">
                            <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($producto->getIdProducto()) ?></td>
                            <td class="list-table__body-cell" data-label="Código"><?= htmlspecialchars($producto->getCodigo()) ?></td>
                            <td class="list-table__body-cell" data-label="Nombre"><?= htmlspecialchars($producto->getNombre()) ?></td>
                            <td class="list-table__body-cell" data-label="Descripción"><?= htmlspecialchars($producto->getDescripcion()) ?></td>
                            <td class="list-table__body-cell" data-label="Unidad de Medida"><?= htmlspecialchars($producto->getUnidadMedida()) ?></td>
                            <td class="list-table__body-cell" data-label="Acciones">
                                <div class="list-table__actions">
                                    <button class="list-table__button list-table__button--edit"
                                            onclick="seleccionarProducto(<?= $producto->getIdProducto() ?>)"
                                            title="Editar producto">
                                        <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                    </button>
                                    <button class="list-table__button list-table__button--delete"
                                            onclick="confirmarEliminarProducto(<?= $producto->getIdProducto() ?>, '<?= htmlspecialchars(addslashes($producto->getNombre())) ?>')"
                                            title="Eliminar producto">
                                        <i class="bi bi-trash list-table__button-icon"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación con JS -->
        <div class="card-footer bg-white py-3">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <div class="paginacion-info">
                        Mostrando <span id="inicio-registros-productos">1</span> a <span id="fin-registros-productos">10</span>
                        de <span id="total-registros-productos"><?= count($productos) ?></span> registros
                    </div>
                </div>
                <div class="col-md-7">
                    <nav aria-label="Paginación de productos">
                        <ul class="pagination justify-content-end mb-0" id="paginacion-productos">
                            <!-- La paginación se generará dinámicamente con JavaScript -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Inicializar datos de productos para paginación
    window.datosProductos = <?= json_encode(array_map(function ($p) {
        return [
            'id' => $p->getIdProducto(),
            'codigo' => $p->getCodigo(),
            'nombre' => $p->getNombre(),
            'descripcion' => $p->getDescripcion(),
            'unidad_medida' => $p->getUnidadMedida()
        ];
    }, $productos)) ?>;
</script>