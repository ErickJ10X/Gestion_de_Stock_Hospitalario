<?php
if (!isset($productos) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="list-header__actions">
    <button id="btn-add-producto" class="list-button list-button--success">
        <i class="bi bi-plus-circle"></i> Nuevo
    </button>
</div>

<div class="table-responsive">
    <table class="list-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Código</th>
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
                        <td class="list-table__body-cell" data-label="Nombre"><?= htmlspecialchars($producto->getNombre()) ?></td>
                        <td class="list-table__body-cell" data-label="Código"><?= htmlspecialchars($producto->getCodigo()) ?></td>
                        <td class="list-table__body-cell" data-label="Descripción"><?= htmlspecialchars($producto->getDescripcion()) ?></td>
                        <td class="list-table__body-cell" data-label="Unidad de Medida"><?= htmlspecialchars($producto->getUnidadMedida()) ?></td>
                        <td class="list-table__body-cell" data-label="Acciones">
                            <div class="list-table__actions">
                                <button class="list-table__button list-table__button--edit btn-edit-producto" data-id="<?= $producto->getIdProducto() ?>">
                                    <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                </button>
                                <button class="list-table__button list-table__button--delete btn-delete-producto" data-id="<?= $producto->getIdProducto() ?>">
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

<!-- Formulario para crear producto -->
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
        <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/productos-actions.php" method="post" class="hospital-form" id="form-crear-producto">
            <input type="hidden" name="action" value="crear_producto">
            <div class="hospital-form__group">
                <label for="nombre-producto-create" class="hospital-form__label">Nombre:</label>
                <input type="text" id="nombre-producto-create" name="nombre" class="hospital-form__input" required>
            </div>
            <div class="hospital-form__group">
                <label for="codigo-producto-create" class="hospital-form__label">Código:</label>
                <input type="text" id="codigo-producto-create" name="codigo" class="hospital-form__input" required>
            </div>
            <div class="hospital-form__group">
                <label for="descripcion-producto-create" class="hospital-form__label">Descripción:</label>
                <textarea id="descripcion-producto-create" name="descripcion" class="hospital-form__input" rows="3"></textarea>
            </div>
            <div class="hospital-form__group">
                <label for="unidad-medida-producto-create" class="hospital-form__label">Unidad de Medida:</label>
                <input type="text" id="unidad-medida-producto-create" name="unidad_medida" class="hospital-form__input" required>
            </div>
            <div class="hospital-card__footer">
                <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-producto">Cancelar</button>
                <button type="submit" class="hospital-form__button hospital-form__button--primary" id="btn-submit-producto">Registrar Producto</button>
            </div>
        </form>
    </div>
</div>

<!-- Formularios para editar y eliminar productos -->
<?php foreach ($productos as $producto): ?>
    <div id="producto-card-edit-<?= $producto->getIdProducto() ?>" class="hospital-card">
        <div class="hospital-card__header hospital-card__header--edit">
            <h3 class="hospital-card__title">Editar Producto</h3>
            <button type="button" class="hospital-card__close">&times;</button>
        </div>
        <div class="hospital-card__body">
            <?php if ($session->hasMessage('modal_error_producto_' . $producto->getIdProducto())): ?>
                <div class="hospital-form__error">
                    <p><?= $session->getMessage('modal_error_producto_' . $producto->getIdProducto()) ?></p>
                </div>
                <?php $session->clearMessage('modal_error_producto_' . $producto->getIdProducto()); ?>
            <?php endif; ?>
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/productos-actions.php" method="post" class="hospital-form" id="form-editar-producto-<?= $producto->getIdProducto() ?>">
                <input type="hidden" name="action" value="editar_producto">
                <input type="hidden" name="id" value="<?= $producto->getIdProducto() ?>">
                <div class="hospital-form__group">
                    <label for="nombre-producto-edit-<?= $producto->getIdProducto() ?>" class="hospital-form__label">Nombre:</label>
                    <input type="text" id="nombre-producto-edit-<?= $producto->getIdProducto() ?>" name="nombre" value="<?= htmlspecialchars($producto->getNombre()) ?>" class="hospital-form__input" required>
                </div>
                <div class="hospital-form__group">
                    <label for="codigo-producto-edit-<?= $producto->getIdProducto() ?>" class="hospital-form__label">Código:</label>
                    <input type="text" id="codigo-producto-edit-<?= $producto->getIdProducto() ?>" name="codigo" value="<?= htmlspecialchars($producto->getCodigo()) ?>" class="hospital-form__input" required>
                </div>
                <div class="hospital-form__group">
                    <label for="descripcion-producto-edit-<?= $producto->getIdProducto() ?>" class="hospital-form__label">Descripción:</label>
                    <textarea id="descripcion-producto-edit-<?= $producto->getIdProducto() ?>" name="descripcion" class="hospital-form__input" rows="3"><?= htmlspecialchars($producto->getDescripcion()) ?></textarea>
                </div>
                <div class="hospital-form__group">
                    <label for="unidad-medida-producto-edit-<?= $producto->getIdProducto() ?>" class="hospital-form__label">Unidad de Medida:</label>
                    <input type="text" id="unidad-medida-producto-edit-<?= $producto->getIdProducto() ?>" name="unidad_medida" value="<?= htmlspecialchars($producto->getUnidadMedida()) ?>" class="hospital-form__input" required>
                </div>
                <div class="hospital-card__footer">
                    <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-edit-producto-<?= $producto->getIdProducto() ?>">Cancelar</button>
                    <button type="submit" class="hospital-form__button hospital-form__button--primary" id="btn-submit-edit-producto-<?= $producto->getIdProducto() ?>">Actualizar Producto</button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="producto-card-delete-<?= $producto->getIdProducto() ?>" class="hospital-card">
        <div class="hospital-card__header hospital-card__header--delete">
            <h3 class="hospital-card__title">Eliminar Producto</h3>
            <button type="button" class="hospital-card__close">&times;</button>
        </div>
        <div class="hospital-card__body">
            <h4>¿Estás seguro de que deseas eliminar este producto?</h4>
            <p class="text-danger">Esta acción no se puede deshacer.</p>
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/productos-actions.php" method="post" id="form-eliminar-producto-<?= $producto->getIdProducto() ?>">
                <input type="hidden" name="action" value="eliminar_producto">
                <input type="hidden" name="id" value="<?= $producto->getIdProducto() ?>">
                <div class="hospital-card__footer">
                    <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-delete-producto-<?= $producto->getIdProducto() ?>">Cancelar</button>
                    <button type="submit" class="hospital-form__button hospital-form__button--danger" id="btn-submit-delete-producto-<?= $producto->getIdProducto() ?>">Confirmar Eliminación</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>
