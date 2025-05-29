<?php
if (!isset($pactos ) || !isset($productos) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="list-header__actions">
    <button id="btn-add-pacto" class="list-button list-button--success">
        <i class="bi bi-plus-circle"></i> Nuevo
    </button>
</div>

<div class="table-responsive">
    <table class="list-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Tipo Ubicación</th>
                <th>Destino</th>
                <th>Cantidad Pactada</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pactos)): ?>
                <tr>
                    <td colspan="6" class="list-table__empty">No hay pactos registrados</td>
                </tr>
            <?php else: ?>
                <?php foreach ($pactos as $pacto): 
                    // Obtener el nombre del producto
                    $nombreProducto = "Desconocido";
                    foreach ($productos as $producto) {
                        if ($producto->getId() == $pacto->getIdProducto()) {
                            $nombreProducto = $producto->getNombre();
                            break;
                        }
                    }
                ?>
                    <tr class="list-table__body-row">
                        <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($pacto->getId()) ?></td>
                        <td class="list-table__body-cell" data-label="Producto"><?= htmlspecialchars($nombreProducto) ?></td>
                        <td class="list-table__body-cell" data-label="Tipo Ubicación"><?= htmlspecialchars($pacto->getTipoUbicacion()) ?></td>
                        <td class="list-table__body-cell" data-label="Destino"><?= htmlspecialchars($pacto->getIdDestino()) ?></td>
                        <td class="list-table__body-cell" data-label="Cantidad Pactada"><?= htmlspecialchars($pacto->getCantidadPactada()) ?></td>
                        <td class="list-table__body-cell" data-label="Acciones">
                            <div class="list-table__actions">
                                <button class="list-table__button list-table__button--edit btn-edit-pacto" data-id="<?= $pacto->getId() ?>">
                                    <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                </button>
                                <button class="list-table__button list-table__button--delete btn-delete-pacto" data-id="<?= $pacto->getId() ?>">
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

<div id="pacto-card-create" class="hospital-card">
    <div class="hospital-card__header hospital-card__header--create">
        <h3 class="hospital-card__title">Nuevo Pacto</h3>
        <button type="button" class="hospital-card__close">&times;</button>
    </div>
    <div class="hospital-card__body">
        <?php if ($session->hasMessage('modal_error_pacto')): ?>
            <div class="hospital-form__error">
                <p><?= $session->getMessage('modal_error_pacto') ?></p>
            </div>
            <?php $session->clearMessage('modal_error_pacto'); ?>
        <?php endif; ?>
        <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/pacto-actions.php" method="post" class="hospital-form" id="form-crear-pacto">
            <input type="hidden" name="action" value="crear_pacto">
            <div class="hospital-form__group">
                <label for="producto-pacto-create" class="hospital-form__label">Producto:</label>
                <select id="producto-pacto-create" name="id_producto" class="hospital-form__input" required>
                    <option value="">Seleccione un producto</option>
                    <?php foreach ($productos as $producto): ?>
                        <option value="<?= $producto->getId() ?>"><?= htmlspecialchars($producto->getNombre()) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="hospital-form__group">
                <label for="tipo-ubicacion-pacto-create" class="hospital-form__label">Tipo de Ubicación:</label>
                <select id="tipo-ubicacion-pacto-create" name="tipo_ubicacion" class="hospital-form__input" required>
                    <option value="">Seleccione un tipo</option>
                    <option value="planta">Planta</option>
                    <option value="botiquin">Botiquín</option>
                    <option value="almacen">Almacén</option>
                </select>
            </div>
            <div class="hospital-form__group">
                <label for="id-destino-pacto-create" class="hospital-form__label">ID Destino:</label>
                <input type="number" id="id-destino-pacto-create" name="id_destino" class="hospital-form__input" required>
            </div>
            <div class="hospital-form__group">
                <label for="cantidad-pactada-create" class="hospital-form__label">Cantidad Pactada:</label>
                <input type="number" id="cantidad-pactada-create" name="cantidad_pactada" class="hospital-form__input" min="1" required>
            </div>
            <div class="hospital-card__footer">
                <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-pacto">Cancelar</button>
                <button type="submit" class="hospital-form__button hospital-form__button--primary" id="btn-submit-pacto">Registrar Pacto</button>
            </div>
        </form>
    </div>
</div>

<?php foreach ($pactos as $pacto): ?>
    <div id="pacto-card-edit-<?= $pacto->getId() ?>" class="hospital-card">
        <div class="hospital-card__header hospital-card__header--edit">
            <h3 class="hospital-card__title">Editar Pacto</h3>
            <button type="button" class="hospital-card__close">&times;</button>
        </div>
        <div class="hospital-card__body">
            <?php if ($session->hasMessage('modal_error_pacto_' . $pacto->getId())): ?>
                <div class="hospital-form__error">
                    <p><?= $session->getMessage('modal_error_pacto_' . $pacto->getId()) ?></p>
                </div>
                <?php $session->clearMessage('modal_error_pacto_' . $pacto->getId()); ?>
            <?php endif; ?>
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/pacto-actions.php" method="post" class="hospital-form" id="form-editar-pacto-<?= $pacto->getId() ?>">
                <input type="hidden" name="action" value="editar_pacto">
                <input type="hidden" name="id" value="<?= $pacto->getId() ?>">
                <div class="hospital-form__group">
                    <label for="producto-pacto-edit-<?= $pacto->getId() ?>" class="hospital-form__label">Producto:</label>
                    <select id="producto-pacto-edit-<?= $pacto->getId() ?>" name="id_producto" class="hospital-form__input" required>
                        <?php foreach ($productos as $producto): ?>
                            <option value="<?= $producto->getId() ?>" <?= $producto->getId() == $pacto->getIdProducto() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($producto->getNombre()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="hospital-form__group">
                    <label for="tipo-ubicacion-pacto-edit-<?= $pacto->getId() ?>" class="hospital-form__label">Tipo de Ubicación:</label>
                    <select id="tipo-ubicacion-pacto-edit-<?= $pacto->getId() ?>" name="tipo_ubicacion" class="hospital-form__input" required>
                        <option value="planta" <?= $pacto->getTipoUbicacion() == 'planta' ? 'selected' : '' ?>>Planta</option>
                        <option value="botiquin" <?= $pacto->getTipoUbicacion() == 'botiquin' ? 'selected' : '' ?>>Botiquín</option>
                        <option value="almacen" <?= $pacto->getTipoUbicacion() == 'almacen' ? 'selected' : '' ?>>Almacén</option>
                    </select>
                </div>
                <div class="hospital-form__group">
                    <label for="id-destino-pacto-edit-<?= $pacto->getId() ?>" class="hospital-form__label">ID Destino:</label>
                    <input type="number" id="id-destino-pacto-edit-<?= $pacto->getId() ?>" name="id_destino" value="<?= htmlspecialchars($pacto->getIdDestino()) ?>" class="hospital-form__input" required>
                </div>
                <div class="hospital-form__group">
                    <label for="cantidad-pactada-edit-<?= $pacto->getId() ?>" class="hospital-form__label">Cantidad Pactada:</label>
                    <input type="number" id="cantidad-pactada-edit-<?= $pacto->getId() ?>" name="cantidad_pactada" value="<?= htmlspecialchars($pacto->getCantidadPactada()) ?>" class="hospital-form__input" min="1" required>
                </div>
                <div class="hospital-card__footer">
                    <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-edit-pacto-<?= $pacto->getId() ?>">Cancelar</button>
                    <button type="submit" class="hospital-form__button hospital-form__button--primary" id="btn-submit-edit-pacto-<?= $pacto->getId() ?>">Actualizar Pacto</button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="pacto-card-delete-<?= $pacto->getId() ?>" class="hospital-card">
        <div class="hospital-card__header hospital-card__header--delete">
            <h3 class="hospital-card__title">Eliminar Pacto</h3>
            <button type="button" class="hospital-card__close">&times;</button>
        </div>
        <div class="hospital-card__body">
            <h4>¿Estás seguro de que deseas eliminar este pacto?</h4>
            <p class="text-danger">Esta acción no se puede deshacer.</p>
            <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/pacto-actions.php" method="post" id="form-eliminar-pacto-<?= $pacto->getId() ?>">
                <input type="hidden" name="action" value="eliminar_pacto">
                <input type="hidden" name="id" value="<?= $pacto->getId() ?>">
                <div class="hospital-card__footer">
                    <button type="button" class="hospital-form__button hospital-form__button--secondary hospital-form__button--cancel" id="btn-cancel-delete-pacto-<?= $pacto->getId() ?>">Cancelar</button>
                    <button type="submit" class="hospital-form__button hospital-form__button--danger" id="btn-submit-delete-pacto-<?= $pacto->getId() ?>">Confirmar Eliminación</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>
