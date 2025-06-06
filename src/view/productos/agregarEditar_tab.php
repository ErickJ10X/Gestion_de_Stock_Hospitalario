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

<!-- Sección de Catálogos -->
<div class="row mt-4" id="catalogosSection">
    <h4 class="mb-3">Gestión de Catálogos de Productos</h4>
    
    <!-- Formulario para agregar catálogo -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="section-title bg-success text-white">
                <i class="fas fa-list-alt me-2"></i> Agregar Producto a Catálogo
            </div>
            <div class="card-body">
                <form id="formCrearCatalogo" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/CatalogosController.php" method="POST" class="card-form">
                    <input type="hidden" name="action" value="crear">
                    
                    <div class="mb-3">
                        <label for="catalogo_producto" class="form-label">Producto <span class="text-danger">*</span></label>
                        <select class="form-select" id="catalogo_producto" name="id_producto" required>
                            <option value="">Seleccione un producto</option>
                            <?php foreach ($productos as $producto): 
                                if ($producto->isActivo()): ?>
                                <option value="<?= $producto->getIdProducto() ?>">
                                    <?= htmlspecialchars($producto->getCodigo()) ?> - <?= htmlspecialchars($producto->getNombre()) ?>
                                </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="catalogo_planta" class="form-label">Planta <span class="text-danger">*</span></label>
                        <select class="form-select" id="catalogo_planta" name="id_planta" required>
                            <option value="">Seleccione una planta</option>
                            <?php foreach ($plantas as $planta): 
                                $hospitalNombre = $planta->getHospital() ? $planta->getHospital()->getNombre() : 'N/A'; ?>
                                <option value="<?= $planta->getIdPlanta() ?>">
                                    <?= htmlspecialchars($planta->getNombre()) ?> 
                                    (<?= htmlspecialchars($hospitalNombre) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div id="catalogo_existente_error" class="alert alert-warning" style="display: none;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Este producto ya existe en el catálogo de la planta seleccionada
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="reset" class="btn btn-secondary me-2">
                            <i class="fas fa-broom"></i> Limpiar
                        </button>
                        <button type="submit" id="btn-guardar-catalogo" class="btn btn-success">
                            <i class="fas fa-plus-circle"></i> Agregar al Catálogo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Formulario para editar catálogo -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="section-title bg-info text-white">
                <i class="fas fa-edit me-2"></i> Editar Catálogo
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="select_catalogo" class="form-label">Seleccionar Catálogo</label>
                    <select class="form-select" id="select_catalogo">
                        <option value="">Seleccione un catálogo para editar</option>
                        <?php foreach ($catalogos as $catalogo): 
                            // Buscar producto y planta asociados
                            $productoNombre = "Producto no disponible";
                            $plantaNombre = "Planta no disponible";
                            
                            foreach ($productos as $p) {
                                if ($p->getIdProducto() == $catalogo->getIdProducto()) {
                                    $productoNombre = $p->getNombre();
                                    break;
                                }
                            }
                            
                            foreach ($plantas as $p) {
                                if ($p->getIdPlanta() == $catalogo->getIdPlanta()) {
                                    $plantaNombre = $p->getNombre();
                                    break;
                                }
                            }
                        ?>
                            <option value="<?= $catalogo->getIdCatalogo() ?>">
                                <?= htmlspecialchars($productoNombre) ?> - <?= htmlspecialchars($plantaNombre) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div id="editar_catalogo_form_container" style="display: none;">
                    <form id="formEditarCatalogo" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/CatalogosController.php" method="POST" class="card-form">
                        <input type="hidden" name="action" value="editar">
                        <input type="hidden" id="editar_catalogo_id" name="id">
                        
                        <div class="mb-3">
                            <label for="editar_catalogo_producto" class="form-label">Producto <span class="text-danger">*</span></label>
                            <select class="form-select" id="editar_catalogo_producto" name="id_producto" required>
                                <option value="">Seleccione un producto</option>
                                <?php foreach ($productos as $producto): ?>
                                <option value="<?= $producto->getIdProducto() ?>">
                                    <?= htmlspecialchars($producto->getCodigo()) ?> - <?= htmlspecialchars($producto->getNombre()) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editar_catalogo_planta" class="form-label">Planta <span class="text-danger">*</span></label>
                            <select class="form-select" id="editar_catalogo_planta" name="id_planta" required>
                                <option value="">Seleccione una planta</option>
                                <?php foreach ($plantas as $planta): 
                                    $hospitalNombre = $planta->getHospital() ? $planta->getHospital()->getNombre() : 'N/A'; ?>
                                    <option value="<?= $planta->getIdPlanta() ?>">
                                        <?= htmlspecialchars($planta->getNombre()) ?> 
                                        (<?= htmlspecialchars($hospitalNombre) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="editar_catalogo_activo" name="activo" checked>
                                <label class="form-check-label" for="editar_catalogo_activo">Activo</label>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" id="cancelar_edicion_catalogo" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-save"></i> Actualizar
                            </button>
                        </div>
                    </form>
                </div>
                
                <div id="editar_catalogo_placeholder" class="text-center text-muted py-4">
                    <i class="fas fa-list-alt fa-3x mb-3"></i>
                    <p>Seleccione un catálogo para editarlo</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para eliminar producto -->
<div class="modal fade" id="eliminarProductoModal" tabindex="-1" aria-labelledby="eliminarProductoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="eliminarProductoModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar el producto <strong id="nombreProductoEliminar"></strong>?</p>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/ProductoController.php" method="POST">
                    <input type="hidden" name="action" value="eliminar">
                    <input type="hidden" name="id" id="id_producto_eliminar">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario de edición de producto
    const selectProducto = document.getElementById('select_producto');
    const editarProductoFormContainer = document.getElementById('editar_producto_form_container');
    const editarProductoPlaceholder = document.getElementById('editar_producto_placeholder');
    const editarProductoId = document.getElementById('editar_producto_id');
    const editarCodigoProducto = document.getElementById('editar_codigo_producto');
    const editarNombreProducto = document.getElementById('editar_nombre_producto');
    const editarDescripcionProducto = document.getElementById('editar_descripcion_producto');
    const editarUnidadMedida = document.getElementById('editar_unidad_medida');
    const cancelarEdicionProducto = document.getElementById('cancelar_edicion_producto');
    
    // Elementos del formulario de edición de catálogo
    const selectCatalogo = document.getElementById('select_catalogo');
    const editarCatalogoFormContainer = document.getElementById('editar_catalogo_form_container');
    const editarCatalogoPlaceholder = document.getElementById('editar_catalogo_placeholder');
    const editarCatalogoId = document.getElementById('editar_catalogo_id');
    const editarCatalogoProducto = document.getElementById('editar_catalogo_producto');
    const editarCatalogoPlanta = document.getElementById('editar_catalogo_planta');
    const editarCatalogoActivo = document.getElementById('editar_catalogo_activo');
    const cancelarEdicionCatalogo = document.getElementById('cancelar_edicion_catalogo');
    
    // Elementos del formulario de creación de catálogo
    const catalogoProducto = document.getElementById('catalogo_producto');
    const catalogoPlanta = document.getElementById('catalogo_planta');
    const catalogoExistenteError = document.getElementById('catalogo_existente_error');
    const btnGuardarCatalogo = document.getElementById('btn-guardar-catalogo');
    
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
    
    // Crear un mapa de los datos de catálogos
    const catalogosData = {};
    <?php foreach ($catalogos as $catalogo): ?>
        catalogosData[<?= $catalogo->getIdCatalogo() ?>] = {
            id: <?= $catalogo->getIdCatalogo() ?>,
            id_producto: <?= $catalogo->getIdProducto() ?>,
            id_planta: <?= $catalogo->getIdPlanta() ?>,
            activo: <?= $catalogo->isActivo() ? 'true' : 'false' ?>
        };
    <?php endforeach; ?>
    
    // Manejar la selección de un producto para editar
    if (selectProducto) {
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
    }
    
    // Manejar el botón de cancelar edición de producto
    if (cancelarEdicionProducto) {
        cancelarEdicionProducto.addEventListener('click', function() {
            selectProducto.value = '';
            editarProductoFormContainer.style.display = 'none';
            editarProductoPlaceholder.style.display = 'block';
        });
    }
    
    // Manejar la selección de un catálogo para editar
    if (selectCatalogo) {
        selectCatalogo.addEventListener('change', function() {
            const catalogoId = this.value;
            
            if (catalogoId) {
                const catalogo = catalogosData[catalogoId];
                if (catalogo) {
                    // Llenar el formulario con los datos del catálogo
                    editarCatalogoId.value = catalogo.id;
                    editarCatalogoProducto.value = catalogo.id_producto;
                    editarCatalogoPlanta.value = catalogo.id_planta;
                    editarCatalogoActivo.checked = catalogo.activo;
                    
                    // Mostrar el formulario y ocultar el placeholder
                    editarCatalogoFormContainer.style.display = 'block';
                    editarCatalogoPlaceholder.style.display = 'none';
                } else {
                    console.error('Error: No se encontraron datos para el catálogo seleccionado');
                }
            } else {
                // Ocultar el formulario y mostrar el placeholder
                editarCatalogoFormContainer.style.display = 'none';
                editarCatalogoPlaceholder.style.display = 'block';
            }
        });
    }
    
    // Manejar el botón de cancelar edición de catálogo
    if (cancelarEdicionCatalogo) {
        cancelarEdicionCatalogo.addEventListener('click', function() {
            selectCatalogo.value = '';
            editarCatalogoFormContainer.style.display = 'none';
            editarCatalogoPlaceholder.style.display = 'block';
        });
    }
    
    // Verificar si un producto ya está en el catálogo de una planta
    if (catalogoProducto && catalogoPlanta) {
        function verificarCatalogoExistente() {
            const idProducto = catalogoProducto.value;
            const idPlanta = catalogoPlanta.value;
            
            if (!idProducto || !idPlanta) {
                catalogoExistenteError.style.display = 'none';
                return;
            }
            
            // Verificar en los datos existentes
            let existe = false;
            <?php foreach ($catalogos as $catalogo): ?>
                if (<?= $catalogo->getIdProducto() ?> == idProducto && 
                    <?= $catalogo->getIdPlanta() ?> == idPlanta) {
                    existe = true;
                }
            <?php endforeach; ?>
            
            if (existe) {
                catalogoExistenteError.style.display = 'block';
                btnGuardarCatalogo.disabled = true;
            } else {
                catalogoExistenteError.style.display = 'none';
                btnGuardarCatalogo.disabled = false;
            }
        }
        
        catalogoProducto.addEventListener('change', verificarCatalogoExistente);
        catalogoPlanta.addEventListener('change', verificarCatalogoExistente);
    }
});
</script>
