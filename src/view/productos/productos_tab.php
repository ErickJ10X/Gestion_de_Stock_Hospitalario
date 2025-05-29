<?php
if (!isset($productos) || !isset($session) || !isset($plantas) || !isset($catalogosController)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="list-header__actions">
    <button id="btn-add-producto" class="list-button list-button--success">
        <i class="bi bi-plus-circle"></i> Nuevo
    </button>
</div>

<?php if (empty($productos)): ?>
    <div class="alert alert-info mt-3">
        No hay productos registrados en el sistema.
    </div>
<?php else: ?>
    <?php if (empty($plantas)): ?>
        <div class="alert alert-warning mt-3">
            No hay plantas registradas para agrupar productos.
        </div>
        
        <div class="table-responsive">
            <h4 class="section-title">Productos sin asignar a planta</h4>
            <table class="list-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr class="list-table__body-row">
                            <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($producto->getId()) ?></td>
                            <td class="list-table__body-cell" data-label="Nombre"><?= htmlspecialchars($producto->getNombre()) ?></td>
                            <td class="list-table__body-cell" data-label="Descripción"><?= htmlspecialchars($producto->getDescripcion()) ?></td>
                            <td class="list-table__body-cell" data-label="Categoría"><?= htmlspecialchars($producto->getCategoria()) ?></td>
                            <td class="list-table__body-cell" data-label="Acciones">
                                <div class="list-table__actions">
                                    <button class="list-table__button list-table__button--edit btn-edit-producto" data-id="<?= $producto->getId() ?>">
                                        <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                    </button>
                                    <button class="list-table__button list-table__button--delete btn-delete-producto" data-id="<?= $producto->getId() ?>">
                                        <i class="bi bi-trash list-table__button-icon"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <!-- Agrupamos los productos por planta -->
        <?php 
        // Lista de productos no asignados a ninguna planta
        $productosNoAsignados = array_values($productos);
        
        // Para cada planta, mostrar los productos asignados
        foreach ($plantas as $planta): 
            // Obtener catálogo de esta planta
            $catalogoPlanta = $catalogosController->getByPlanta($planta->getIdPlanta());
            $productosDePlanta = [];
            
            if (!$catalogoPlanta['error'] && !empty($catalogoPlanta['catalogos'])) {
                foreach ($catalogoPlanta['catalogos'] as $catalogo) {
                    foreach ($productos as $key => $producto) {
                        if ($producto->getId() == $catalogo->getIdProducto()) {
                            $productosDePlanta[] = $producto;
                            // Quitar de la lista de no asignados
                            foreach ($productosNoAsignados as $index => $prodNoAsig) {
                                if ($prodNoAsig->getId() == $producto->getId()) {
                                    unset($productosNoAsignados[$index]);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
            // Re-indexar el array después de unset
            $productosNoAsignados = array_values($productosNoAsignados);
        ?>
        
        <div class="planta-section mb-4">
            <h4 class="section-title">Productos en <?= htmlspecialchars($planta->getNombre()) ?></h4>
            
            <?php if (empty($productosDePlanta)): ?>
                <div class="alert alert-light">
                    No hay productos asignados a esta planta.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="list-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Categoría</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productosDePlanta as $producto): ?>
                                <tr class="list-table__body-row">
                                    <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($producto->getId()) ?></td>
                                    <td class="list-table__body-cell" data-label="Nombre"><?= htmlspecialchars($producto->getNombre()) ?></td>
                                    <td class="list-table__body-cell" data-label="Descripción"><?= htmlspecialchars($producto->getDescripcion()) ?></td>
                                    <td class="list-table__body-cell" data-label="Categoría"><?= htmlspecialchars($producto->getCategoria()) ?></td>
                                    <td class="list-table__body-cell" data-label="Acciones">
                                        <div class="list-table__actions">
                                            <button class="list-table__button list-table__button--edit btn-edit-producto" data-id="<?= $producto->getId() ?>">
                                                <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                            </button>
                                            <button class="list-table__button list-table__button--delete btn-delete-producto" data-id="<?= $producto->getId() ?>">
                                                <i class="bi bi-trash list-table__button-icon"></i> Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        
        <!-- Productos no asignados a ninguna planta -->
        <?php if (!empty($productosNoAsignados)): ?>
            <div class="planta-section mt-4">
                <h4 class="section-title">Productos sin asignar a planta</h4>
                <div class="table-responsive">
                    <table class="list-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Categoría</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productosNoAsignados as $producto): ?>
                                <tr class="list-table__body-row">
                                    <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($producto->getId()) ?></td>
                                    <td class="list-table__body-cell" data-label="Nombre"><?= htmlspecialchars($producto->getNombre()) ?></td>
                                    <td class="list-table__body-cell" data-label="Descripción"><?= htmlspecialchars($producto->getDescripcion()) ?></td>
                                    <td class="list-table__body-cell" data-label="Categoría"><?= htmlspecialchars($producto->getCategoria()) ?></td>
                                    <td class="list-table__body-cell" data-label="Acciones">
                                        <div class="list-table__actions">
                                            <button class="list-table__button list-table__button--edit btn-edit-producto" data-id="<?= $producto->getId() ?>">
                                                <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                            </button>
                                            <button class="list-table__button list-table__button--delete btn-delete-producto" data-id="<?= $producto->getId() ?>">
                                                <i class="bi bi-trash list-table__button-icon"></i> Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>

<div id="producto-card-create" class="hospital-card">
    <div class="hospital-card__header hospital-card__header--create">
        <h3 class="hospital-card__title">Nuevo Producto</h3>
        <button type="button" class="hospital-card__close">&times;</button>
    </div>
    <div class="hospital-card__body">
        <?php if ($session->hasMessage('modal_error_producto')): ?>
            <div class="hospital-form__error">
                <p><?= $session->getMessage('modal_error_producto') ?></p>
            </div>
            <?php $session->clearMessage('modal_error_producto'); ?>
        <?php endif; ?>
        <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/producto-actions.php" method="post" class="hospital-form" id="form-crear-producto">
            <input type="hidden" name="action" value="crear_producto">
            <div class="hospital-form__group">
                <label for="nombre-producto-create" class="hospital-form__label">Nombre:</label>
                <input type="text" id="nombre-producto-create" name="nombre" class="hospital-form__input" required>
            </div>
            <div class="hospital-form__group">
                <label for="descripcion-producto-create" class="hospital-form__label">Descripción:</label>
                <textarea id="descripcion-producto-create" name="descripcion" class="hospital-form__input" required></textarea>
            </div>
            <div class="hospital-form__group">
                <label for="categoria-producto-create" class="hospital-form__label">Categoría:</label>
                <input type="text" id="categoria-producto-create" name="categoria" class="hospital-form__input" required>
            </div>
            <div class="hospital-card__footer">
                <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-producto">Cancelar</button>
                <button type="submit" class="hospital-form__button hospital-form__button--primary" id="btn-submit-producto">Registrar Producto</button>
            </div>
        </form>
    </div>
</div>
