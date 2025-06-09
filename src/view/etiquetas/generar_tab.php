<?php
if(!isset($productoController) || !isset($productos) || !isset($reposiciones)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h5 class="mb-0"><i class="fas fa-tags"></i> Generación de Etiquetas</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/EtiquetasController.php" id="formularioGenerarEtiqueta">
            <input type="hidden" name="action" value="crear">
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="id_producto" class="form-label">Producto <span class="text-danger">*</span></label>
                    <select class="form-select" id="id_producto" name="id_producto" required>
                        <option value="">Seleccione un producto</option>
                        <?php if (!empty($productos)): ?>
                            <?php foreach ($productos as $producto): ?>
                                <option value="<?= $producto->getIdProducto(); ?>">
                                    <?= htmlspecialchars($producto->getNombre()); ?> 
                                    <?php if (method_exists($producto, 'getReferencia') && $producto->getReferencia()): ?>
                                        - <?= htmlspecialchars($producto->getReferencia()); ?>
                                    <?php elseif (method_exists($producto, 'getDescripcion') && $producto->getDescripcion()): ?>
                                        - <?= htmlspecialchars($producto->getDescripcion()); ?>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <div class="form-text">
                        <i class="fas fa-info-circle"></i> 
                        Productos disponibles: <?= count($productos) ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="id_reposicion" class="form-label">Reposición <span class="text-danger">*</span></label>
                    <select class="form-select" id="id_reposicion" name="id_reposicion" required>
                        <option value="">Seleccione una reposición</option>
                        <?php if (!empty($reposiciones)): ?>
                            <?php foreach ($reposiciones as $reposicion): ?>
                                <option value="<?= $reposicion->getId(); ?>">
                                    ID: <?= $reposicion->getId(); ?> - 
                                    Fecha: <?= $reposicion->getFecha()->format('d/m/Y H:i'); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No hay reposiciones disponibles</option>
                        <?php endif; ?>
                    </select>
                    <div class="form-text">
                        <i class="fas fa-info-circle"></i> 
                        Reposiciones disponibles: <?= count($reposiciones) ?>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-12">
                    <label class="form-label">Tipo de Etiqueta <span class="text-danger">*</span></label>
                    <div class="etiquetas-container">
                        <div class="etiqueta-option">
                            <input type="radio" id="normal_informativa" name="tipo_prioridad" value="Informativa,Normal" required>
                            <label for="normal_informativa" class="etiqueta normal">
                                <div class="etiqueta-content">
                                    <div class="color-indicator blue"></div>
                                    <span>Normal Informativa</span>
                                </div>
                            </label>
                        </div>
                        
                        <div class="etiqueta-option">
                            <input type="radio" id="normal_rfid" name="tipo_prioridad" value="RFID,Normal">
                            <label for="normal_rfid" class="etiqueta normal">
                                <div class="etiqueta-content">
                                    <div class="color-indicator blue"></div>
                                    <span>Normal RFID</span>
                                </div>
                            </label>
                        </div>
                        
                        <div class="etiqueta-option">
                            <input type="radio" id="urgente_informativa" name="tipo_prioridad" value="Informativa,Urgente">
                            <label for="urgente_informativa" class="etiqueta urgente">
                                <div class="etiqueta-content">
                                    <div class="color-indicator red"></div>
                                    <span>Urgente Informativa</span>
                                </div>
                            </label>
                        </div>
                        
                        <div class="etiqueta-option">
                            <input type="radio" id="urgente_rfid" name="tipo_prioridad" value="RFID,Urgente">
                            <label for="urgente_rfid" class="etiqueta urgente">
                                <div class="etiqueta-content">
                                    <div class="color-indicator red"></div>
                                    <span>Urgente RFID</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Campos ocultos que se actualizarán con JavaScript -->
            <input type="hidden" id="tipo" name="tipo" value="">
            <input type="hidden" id="prioridad" name="prioridad" value="">
            
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="list-table__button list-table__button--add">
                        <i class="bi bi-printer"></i> Generar Etiqueta
                    </button>
                    <button type="button" class="list-table__button list-table__button--view" data-bs-toggle="modal" data-bs-target="#previewEtiquetaModal">
                        <i class="bi bi-eye"></i> Vista Previa
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal para vista previa de etiqueta en generación -->
<div class="modal fade" id="previewEtiquetaModal" tabindex="-1" aria-labelledby="previewEtiquetaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewEtiquetaModalLabel">
                    <i class="fas fa-eye me-2"></i> Vista previa de etiqueta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="preview-container">
                    <div id="etiqueta-preview" class="etiqueta-preview normal">
                        <div class="preview-header">
                            <div class="color-indicator blue"></div>
                            <span id="preview-tipo-prioridad">Normal Informativa</span>
                        </div>
                        <div class="preview-body">
                            <p><strong>Producto:</strong> <span id="preview-producto">Seleccione un producto</span></p>
                            <p><strong>Referencia:</strong> <span id="preview-referencia">-</span></p>
                            <p><strong>Reposición ID:</strong> <span id="preview-reposicion">-</span></p>
                            <p><strong>Fecha:</strong> <span id="preview-fecha"><?= date('d/m/Y'); ?></span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Manejar la selección de tipo de etiqueta
        const radioButtons = document.querySelectorAll('input[name="tipo_prioridad"]');
        const tipoInput = document.getElementById('tipo');
        const prioridadInput = document.getElementById('prioridad');
        const etiquetaPreview = document.getElementById('etiqueta-preview');
        const previewTipoPrioridad = document.getElementById('preview-tipo-prioridad');
        const colorIndicatorPreview = etiquetaPreview.querySelector('.color-indicator');
        
        // Función para actualizar los campos ocultos y la vista previa
        function actualizarTipoPrioridad(tipo, prioridad) {
            tipoInput.value = tipo;
            prioridadInput.value = prioridad;
            
            // Actualizar vista previa en el modal
            previewTipoPrioridad.textContent = prioridad + ' ' + tipo;
            
            if (prioridad === 'Normal') {
                etiquetaPreview.className = 'etiqueta-preview normal';
                colorIndicatorPreview.className = 'color-indicator blue';
            } else {
                etiquetaPreview.className = 'etiqueta-preview urgente';
                colorIndicatorPreview.className = 'color-indicator red';
            }
        }
        
        // Evento para los radio buttons
        radioButtons.forEach(radio => {
            radio.addEventListener('change', function() {
                const [tipo, prioridad] = this.value.split(',');
                actualizarTipoPrioridad(tipo, prioridad);
            });
        });
        
        // Establecer un valor por defecto al cargar
        if (radioButtons.length > 0) {
            const defaultRadio = radioButtons[0];
            defaultRadio.checked = true;
            const [tipo, prioridad] = defaultRadio.value.split(',');
            actualizarTipoPrioridad(tipo, prioridad);
        }
        
        // Actualizar la vista previa con el producto seleccionado
        const productoSelect = document.getElementById('id_producto');
        const previewProducto = document.getElementById('preview-producto');
        const previewReferencia = document.getElementById('preview-referencia');
        
        if (productoSelect) {
            productoSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                
                if (selectedOption.value) {
                    const productoInfo = selectedOption.textContent.split(' - ');
                    previewProducto.textContent = productoInfo[0].trim();
                    previewReferencia.textContent = productoInfo.length > 1 ? productoInfo[1].trim() : '-';
                } else {
                    previewProducto.textContent = 'Seleccione un producto';
                    previewReferencia.textContent = '-';
                }
            });
        }
        
        // Actualizar la vista previa con la reposición seleccionada
        const reposicionSelect = document.getElementById('id_reposicion');
        const previewReposicion = document.getElementById('preview-reposicion');
        
        if (reposicionSelect) {
            reposicionSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    previewReposicion.textContent = selectedOption.value;
                } else {
                    previewReposicion.textContent = '-';
                }
            });
        }
        
        // Validación del formulario
        const form = document.getElementById('formularioGenerarEtiqueta');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Verificar que se hayan elegido los valores requeridos
                if (!tipoInput.value || !prioridadInput.value) {
                    e.preventDefault();
                    alert('Por favor seleccione un tipo de etiqueta');
                    return false;
                }
                
                if (!productoSelect.value) {
                    e.preventDefault();
                    alert('Por favor seleccione un producto');
                    return false;
                }
                
                if (!reposicionSelect.value) {
                    e.preventDefault();
                    alert('Por favor seleccione una reposición');
                    return false;
                }
                
                return true;
            });
        }
    });
</script>
