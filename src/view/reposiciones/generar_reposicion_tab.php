<?php
if(!isset($reposicionesController) || !isset($almacenes) || !isset($productos) || !isset($botiquines)) {
    die('No se puede acceder directamente a este archivo.');
}
?>

<div class="tab-header">
    <h2>Generar Nueva Orden de Reposición</h2>
</div>

<div class="card-form generar-reposicion-form">
    <form id="nueva-reposicion-form" method="post" action="">
        <div class="form-group">
            <label for="id_producto">Producto a Reponer *</label>
            <select id="id_producto" name="id_producto" class="form-control" required>
                <option value="">Seleccione un producto</option>
                <?php foreach ($productos as $producto): ?>
                    <option value="<?= $producto->getIdProducto() ?>" data-unidad="<?= $producto->getUnidadMedida() ?>">
                        <?= $producto->getCodigo() ?> - <?= $producto->getNombre() ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group form-group--half">
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

            <div class="form-group form-group--half">
                <label for="hacia_botiquin">Hasta Botiquín *</label>
                <select id="hacia_botiquin" name="hacia_botiquin" class="form-control" required>
                    <option value="">Seleccione un botiquín</option>
                    <?php foreach ($botiquines as $botiquin): ?>
                        <option value="<?= $botiquin->getIdBotiquin() ?>">
                            <?= $botiquin->getNombre() ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group form-group--half">
                <label for="cantidad_repuesta">Cantidad a Reponer *</label>
                <div class="input-group">
                    <input type="number" id="cantidad_repuesta" name="cantidad_repuesta" class="form-control" min="1" required>
                    <div class="input-group-append">
                        <span class="input-group-text" id="unidad-medida">unidades</span>
                    </div>
                </div>
            </div>

            <div class="form-group form-group--half">
                <label for="fecha">Fecha de Reposición</label>
                <input type="datetime-local" id="fecha" name="fecha" class="form-control">
                <small class="form-text text-muted">Si se deja en blanco, se usará la fecha actual</small>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group form-group--half">
                <div class="checkbox-container">
                    <input type="checkbox" id="urgente" name="urgente" class="form-check-input" value="1">
                    <label for="urgente" class="checkbox-label">
                        <span class="checkbox-label-text">Marcar como urgente</span>
                    </label>
                </div>
            </div>

            <!-- Incluimos el estado como un campo oculto, por defecto en pendiente (false/0) -->
            <input type="hidden" id="estado" name="estado" value="0">
        </div>

        <div class="form-row">
            <div class="form-group form-group--full">
                <label for="notas">Notas adicionales</label>
                <textarea id="notas" name="notas" class="form-control" rows="3" placeholder="Notas o comentarios adicionales sobre esta reposición"></textarea>
            </div>
        </div>

        <div class="form-section">
            <h3 class="form-section-title">Información del Producto</h3>
            <div class="product-info" id="product-info">
                <p class="no-product-selected">Seleccione un producto para ver su información</p>
                <div class="product-details" style="display: none;">
                    <div class="product-detail">
                        <span class="detail-label">Código:</span>
                        <span class="detail-value" id="producto-codigo"></span>
                    </div>
                    <div class="product-detail">
                        <span class="detail-label">Nombre:</span>
                        <span class="detail-value" id="producto-nombre"></span>
                    </div>
                    <div class="product-detail">
                        <span class="detail-label">Unidad de Medida:</span>
                        <span class="detail-value" id="producto-unidad"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn btn-primary" id="generar-reposicion-btn">Generar Reposición</button>
            <button type="reset" class="btn btn-secondary" id="limpiar-form-btn">Limpiar Formulario</button>
        </div>
    </form>
</div>

<div class="confirmacion-modal" id="confirmacion-modal" style="display:none;">
    <div class="confirmacion-modal-content">
        <div class="confirmacion-modal-header">
            <h3>Confirmación de Reposición</h3>
            <button type="button" class="confirmacion-modal-close">&times;</button>
        </div>
        <div class="confirmacion-modal-body">
            <p>Por favor confirme los datos de la reposición:</p>
            <ul class="confirmacion-datos">
                <li><strong>Producto:</strong> <span id="confirm-producto"></span></li>
                <li><strong>Almacén Origen:</strong> <span id="confirm-almacen"></span></li>
                <li><strong>Botiquín Destino:</strong> <span id="confirm-botiquin"></span></li>
                <li><strong>Cantidad:</strong> <span id="confirm-cantidad"></span></li>
                <li><strong>Fecha:</strong> <span id="confirm-fecha"></span></li>
                <li><strong>Urgencia:</strong> <span id="confirm-urgencia"></span></li>
                <li><strong>Estado:</strong> <span id="confirm-estado">Pendiente</span></li>
                <li><strong>Notas:</strong> <span id="confirm-notas"></span></li>
            </ul>
            <p>¿Desea proceder con la creación de esta reposición?</p>
        </div>
        <div class="confirmacion-modal-footer">
            <button type="button" class="btn btn-primary" id="confirmar-reposicion">Confirmar</button>
            <button type="button" class="btn btn-secondary confirmacion-modal-close">Cancelar</button>
        </div>
    </div>
</div>

<style>
    .generar-reposicion-form {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .form-row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -10px;
    }

    .form-group--half {
        flex: 0 0 calc(50% - 20px);
        margin: 0 10px 15px;
    }

    .form-group--full {
        flex: 0 0 calc(100% - 20px);
        margin: 0 10px 15px;
    }

    .input-group {
        display: flex;
        align-items: center;
    }

    .input-group-append {
        display: flex;
        align-items: center;
        margin-left: -1px;
    }

    .input-group-text {
        padding: 0.375rem 0.75rem;
        margin-bottom: 0;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #495057;
        text-align: center;
        white-space: nowrap;
        background-color: #e9ecef;
        border: 1px solid #ced4da;
        border-radius: 0 0.25rem 0.25rem 0;
    }

    .form-section {
        margin-top: 20px;
        border-top: 1px solid #eee;
        padding-top: 15px;
    }

    .form-section-title {
        font-size: 18px;
        margin-bottom: 15px;
        color: #333;
    }

    .product-info {
        background-color: #f9f9f9;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .no-product-selected {
        color: #777;
        font-style: italic;
        text-align: center;
    }

    .product-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .product-detail {
        margin-bottom: 5px;
    }

    .detail-label {
        font-weight: 600;
        color: #555;
        margin-right: 5px;
    }

    .checkbox-container {
        display: flex;
        align-items: center;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        margin-bottom: 0;
        cursor: pointer;
    }

    .checkbox-label-text {
        margin-left: 5px;
    }

    .confirmacion-modal {
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

    .confirmacion-modal-content {
        background: white;
        width: 500px;
        max-width: 90%;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    .confirmacion-modal-header {
        background: #f5f5f5;
        padding: 15px 20px;
        border-bottom: 1px solid #ddd;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .confirmacion-modal-header h3 {
        margin: 0;
        font-size: 18px;
    }

    .confirmacion-modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #888;
    }

    .confirmacion-modal-body {
        padding: 20px;
    }

    .confirmacion-datos {
        background: #f9f9f9;
        border-radius: 5px;
        padding: 15px;
        margin: 15px 0;
        list-style-type: none;
    }

    .confirmacion-datos li {
        margin-bottom: 8px;
        padding-bottom: 8px;
        border-bottom: 1px solid #eee;
    }

    .confirmacion-datos li:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .confirmacion-modal-footer {
        background: #f5f5f5;
        padding: 15px 20px;
        border-top: 1px solid #ddd;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('nueva-reposicion-form');
        const productoSelect = document.getElementById('id_producto');
        const unidadSpan = document.getElementById('unidad-medida');
        const productoInfo = document.getElementById('product-info');
        const productDetails = document.querySelector('.product-details');
        const noProductSelected = document.querySelector('.no-product-selected');
        const confirmacionModal = document.getElementById('confirmacion-modal');
        const reposicionOverlay = document.querySelector('.reposicion-overlay');

        if (productoSelect && unidadSpan) {
            productoSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const unidad = selectedOption.dataset.unidad || 'unidades';
                unidadSpan.textContent = unidad;

                // Mostrar detalles del producto si está seleccionado
                if (this.value && productoInfo) {
                    if (productDetails) productDetails.style.display = 'grid';
                    if (noProductSelected) noProductSelected.style.display = 'none';

                    document.getElementById('producto-codigo').textContent = selectedOption.textContent.split(' - ')[0];
                    document.getElementById('producto-nombre').textContent = selectedOption.textContent.split(' - ')[1];
                    document.getElementById('producto-unidad').textContent = unidad;
                } else {
                    if (productDetails) productDetails.style.display = 'none';
                    if (noProductSelected) noProductSelected.style.display = 'block';
                }
            });
        }

        // Manejar el formulario de generación de reposición
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validar formulario
                const producto = document.getElementById('id_producto').value;
                const desdeAlmacen = document.getElementById('desde_almacen').value;
                const hastaBotiquin = document.getElementById('hacia_botiquin').value;
                const cantidad = document.getElementById('cantidad_repuesta').value;

                if (!producto || !desdeAlmacen || !hastaBotiquin || !cantidad || cantidad <= 0) {
                    alert('Por favor complete todos los campos obligatorios correctamente.');
                    return false;
                }

                // Mostrar confirmación
                if (confirmacionModal) {
                    // Llenar datos de confirmación
                    document.getElementById('confirm-producto').textContent =
                        document.getElementById('id_producto').options[document.getElementById('id_producto').selectedIndex].text;
                    document.getElementById('confirm-almacen').textContent =
                        document.getElementById('desde_almacen').options[document.getElementById('desde_almacen').selectedIndex].text;
                    document.getElementById('confirm-botiquin').textContent =
                        document.getElementById('hacia_botiquin').options[document.getElementById('hacia_botiquin').selectedIndex].text;
                    document.getElementById('confirm-cantidad').textContent = `${cantidad} ${document.getElementById('unidad-medida').textContent}`;
                    document.getElementById('confirm-fecha').textContent =
                        document.getElementById('fecha').value || 'Fecha actual';
                    document.getElementById('confirm-urgencia').textContent =
                        document.getElementById('urgente').checked ? 'URGENTE' : 'Normal';

                    // Agregar notas
                    const notasTexto = document.getElementById('notas').value;
                    document.getElementById('confirm-notas').textContent = notasTexto ? notasTexto : 'Sin notas adicionales';

                    // Agregar campo action
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'crear';
                    form.appendChild(actionInput);

                    // Mostrar modal
                    confirmacionModal.style.display = 'flex';
                    if (reposicionOverlay) reposicionOverlay.style.display = 'block';

                    // Manejar confirmación
                    document.getElementById('confirmar-reposicion').onclick = function() {
                        form.submit();
                    };

                    // Cerrar modal
                    document.querySelectorAll('.confirmacion-modal-close').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            confirmacionModal.style.display = 'none';
                            if (reposicionOverlay) reposicionOverlay.style.display = 'none';
                        });
                    });
                } else {
                    // Si no hay modal, enviar formulario directamente
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'crear';
                    form.appendChild(actionInput);
                    form.submit();
                }
            });
        }
    });
</script>