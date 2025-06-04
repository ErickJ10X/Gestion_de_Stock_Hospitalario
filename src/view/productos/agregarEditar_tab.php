<?php
if (!isset($productos) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="row">
    <!-- Formulario para agregar producto -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="section-title bg-primary text-white">
                <i class="fas fa-box-open me-2"></i> Crear Producto
            </div>
            <div class="card-body">
                <form id="formCrearProducto" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/ProductoController.php" method="POST" class="card-form">
                    <input type="hidden" name="action" value="crear">
                    
                    <div class="mb-3">
                        <label for="codigo_producto" class="form-label">Código <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="codigo_producto" name="codigo" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nombre_producto" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre_producto" name="nombre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion_producto" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion_producto" name="descripcion" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="unidad_medida" class="form-label">Unidad de Medida <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="unidad_medida" name="unidad_medida" required>
                        <div class="form-text">Ejemplo: Unidad, Caja, Litro, Kilogramo, etc.</div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="reset" class="btn btn-secondary me-2">
                            <i class="fas fa-broom"></i> Limpiar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Formulario para editar producto -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="section-title bg-warning text-dark">
                <i class="fas fa-edit me-2"></i> Editar Producto
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="select_producto" class="form-label">Seleccionar Producto</label>
                    <select class="form-select" id="select_producto">
                        <option value="">Seleccione un producto para editar</option>
                        <?php foreach ($productos as $producto): ?>
                            <option value="<?= $producto->getIdProducto() ?>">
                                <?= htmlspecialchars($producto->getCodigo()) ?> - <?= htmlspecialchars($producto->getNombre()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div id="editar_producto_form_container" style="display: none;">
                    <form id="formEditarProducto" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/ProductoController.php" method="POST" class="card-form">
                        <input type="hidden" name="action" value="editar">
                        <input type="hidden" id="editar_producto_id" name="id">
                        
                        <div class="mb-3">
                            <label for="editar_codigo_producto" class="form-label">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editar_codigo_producto" name="codigo" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editar_nombre_producto" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editar_nombre_producto" name="nombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editar_descripcion_producto" class="form-label">Descripción</label>
                            <textarea class="form-control" id="editar_descripcion_producto" name="descripcion" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editar_unidad_medida" class="form-label">Unidad de Medida <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editar_unidad_medida" name="unidad_medida" required>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" id="cancelar_edicion_producto" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar
                            </button>
                        </div>
                    </form>
                </div>
                
                <div id="editar_producto_placeholder" class="text-center text-muted py-4">
                    <i class="fas fa-box-open fa-3x mb-3"></i>
                    <p>Seleccione un producto para editarlo</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario de edición
    const selectProducto = document.getElementById('select_producto');
    const editarProductoFormContainer = document.getElementById('editar_producto_form_container');
    const editarProductoPlaceholder = document.getElementById('editar_producto_placeholder');
    const editarProductoId = document.getElementById('editar_producto_id');
    const editarCodigoProducto = document.getElementById('editar_codigo_producto');
    const editarNombreProducto = document.getElementById('editar_nombre_producto');
    const editarDescripcionProducto = document.getElementById('editar_descripcion_producto');
    const editarUnidadMedida = document.getElementById('editar_unidad_medida');
    const cancelarEdicionProducto = document.getElementById('cancelar_edicion_producto');
    
    // Crear un mapa de los datos de productos
    const productosData = {};
    <?php foreach ($productos as $producto): ?>
        productosData[<?= $producto->getIdProducto() ?>] = {
            id: <?= $producto->getIdProducto() ?>,
            codigo: '<?= addslashes($producto->getCodigo()) ?>',
            nombre: '<?= addslashes($producto->getNombre()) ?>',
            descripcion: '<?= addslashes($producto->getDescripcion()) ?>',
            unidad_medida: '<?= addslashes($producto->getUnidadMedida()) ?>'
        };
    <?php endforeach; ?>
    
    // Manejar la selección de un producto para editar
    selectProducto.addEventListener('change', function() {
        const productoId = this.value;
        
        if (productoId) {
            const producto = productosData[productoId];
            if (producto) {
                // Llenar el formulario con los datos del producto
                editarProductoId.value = producto.id;
                editarCodigoProducto.value = producto.codigo;
                editarNombreProducto.value = producto.nombre;
                editarDescripcionProducto.value = producto.descripcion;
                editarUnidadMedida.value = producto.unidad_medida;
                
                // Mostrar el formulario y ocultar el placeholder
                editarProductoFormContainer.style.display = 'block';
                editarProductoPlaceholder.style.display = 'none';
            } else {
                console.error('Error: No se encontraron datos para el producto seleccionado');
            }
        } else {
            // Ocultar el formulario y mostrar el placeholder
            editarProductoFormContainer.style.display = 'none';
            editarProductoPlaceholder.style.display = 'block';
        }
    });
    
    // Manejar el botón de cancelar edición
    cancelarEdicionProducto.addEventListener('click', function() {
        selectProducto.value = '';
        editarProductoFormContainer.style.display = 'none';
        editarProductoPlaceholder.style.display = 'block';
    });
});
</script>
