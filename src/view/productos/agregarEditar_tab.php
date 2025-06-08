<?php
if (!isset($productos) || !isset($session) || !isset($plantas) || !isset($catalogos)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="productos-container">
    <div class="form-toggle-buttons mb-4">
        <button type="button" class="btn form-toggle-btn active" data-target="crearProductoForm">
            <i class="fas fa-box-open"></i> Crear Producto
        </button>
        <button type="button" class="btn form-toggle-btn" data-target="editarProductoForm">
            <i class="fas fa-edit"></i> Editar Producto
        </button>
        <button type="button" class="btn form-toggle-btn" data-target="crearCatalogoForm">
            <i class="fas fa-list-alt"></i> Crear Catálogo
        </button>
        <button type="button" class="btn form-toggle-btn" data-target="editarCatalogoForm">
            <i class="fas fa-edit"></i> Editar Catálogo
        </button>
    </div>

    <div class="row">
        <!-- Formulario para crear producto -->
        <div class="col-md-12 form-section" id="crearProductoForm" style="display: block;">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form id="formCrearProducto" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/ProductoController.php" method="POST" class="card-form">
                        <input type="hidden" name="action" value="crear">

                        <div class="row">
                            <div class="form-group mb-3">
                                <label for="codigo_producto" class="form-label">Código <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="codigo_producto" name="codigo" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group mb-3">
                                <label for="nombre_producto" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre_producto" name="nombre" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group mb-3">
                                <label for="descripcion_producto" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion_producto" name="descripcion" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group mb-3">
                                <label for="unidad_medida" class="form-label">Unidad de Medida <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="unidad_medida" name="unidad_medida" required>
                                <div class="form-text">Ejemplo: Unidad, Caja, Litro, Kilogramo, etc.</div>
                            </div>
                        </div>

                        <div class="form-group mt-4 text-center">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-broom"></i> Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Formulario para editar producto -->
        <div class="col-md-12 form-section" id="editarProductoForm" style="display: none;">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <?php if (count($productos) > 0): ?>
                        <!-- Selector de producto -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="select_producto" class="form-label">Seleccione un producto</label>
                                    <select class="form-select" id="select_producto">
                                        <option value="">Seleccione un producto para editar</option>
                                        <?php foreach ($productos as $producto): ?>
                                            <option value="<?= $producto->getIdProducto() ?>">
                                                <?= htmlspecialchars($producto->getCodigo()) ?> - <?= htmlspecialchars($producto->getNombre()) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Seleccione un producto para editar sus datos</div>
                                </div>
                            </div>
                        </div>

                        <!-- Mensaje de selección inicial -->
                        <div id="mensajeSeleccionProducto" class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i> Seleccione un producto para editar
                        </div>

                        <!-- Formulario de edición -->
                        <div id="editar_producto_form_container" style="display: none;">
                            <form id="formEditarProducto" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/ProductoController.php" method="POST" class="card-form">
                                <input type="hidden" name="action" value="editar">
                                <input type="hidden" id="editar_producto_id" name="id">

                                <div class="row">
                                    <div class="form-group mb-3">
                                        <label for="editar_codigo_producto" class="form-label">Código <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editar_codigo_producto" name="codigo" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group mb-3">
                                        <label for="editar_nombre_producto" class="form-label">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editar_nombre_producto" name="nombre" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group mb-3">
                                        <label for="editar_descripcion_producto" class="form-label">Descripción</label>
                                        <textarea class="form-control" id="editar_descripcion_producto" name="descripcion" rows="3"></textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group mb-3">
                                        <label for="editar_unidad_medida" class="form-label">Unidad de Medida <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editar_unidad_medida" name="unidad_medida" required>
                                    </div>
                                </div>

                                <div class="form-group mt-4 text-center">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Actualizar
                                    </button>
                                    <button type="button" id="cancelar_edicion_producto" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> No hay productos disponibles para editar.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Formulario para crear catálogo -->
        <div class="col-md-12 form-section" id="crearCatalogoForm" style="display: none;">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form id="formCrearCatalogo" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/CatalogosController.php" method="POST" class="card-form">
                        <input type="hidden" name="action" value="crear">

                        <div class="row">
                            <div class="form-group mb-3">
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
                        </div>

                        <div class="row">
                            <div class="form-group mb-3">
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
                        </div>

                        <div id="catalogo_existente_error" class="alert alert-warning" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Este producto ya existe en el catálogo de la planta seleccionada
                        </div>

                        <div class="form-group mt-4 text-center">
                            <button type="submit" id="btn-guardar-catalogo" class="btn btn-success">
                                <i class="fas fa-plus-circle"></i> Agregar al Catálogo
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-broom"></i> Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Formulario para editar catálogo -->
        <div class="col-md-12 form-section" id="editarCatalogoForm" style="display: none;">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <?php if (count($catalogos) > 0): ?>
                        <!-- Selector de catálogo -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
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
                                    <div class="form-text">Seleccione un catálogo para editar sus datos</div>
                                </div>
                            </div>
                        </div>

                        <!-- Mensaje de selección inicial -->
                        <div id="mensajeSeleccionCatalogo" class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i> Seleccione un catálogo para editar
                        </div>

                        <!-- Formulario de edición -->
                        <div id="editar_catalogo_form_container" style="display: none;">
                            <form id="formEditarCatalogo" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/CatalogosController.php" method="POST" class="card-form">
                                <input type="hidden" name="action" value="editar">
                                <input type="hidden" id="editar_catalogo_id" name="id">

                                <div class="row">
                                    <div class="form-group mb-3">
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
                                </div>

                                <div class="row">
                                    <div class="form-group mb-3">
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
                                </div>

                                <div class="row">
                                    <div class="form-group mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="editar_catalogo_activo" name="activo" checked>
                                            <label class="form-check-label" for="editar_catalogo_activo">Activo</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-4 text-center">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-save"></i> Actualizar
                                    </button>
                                    <button type="button" id="cancelar_edicion_catalogo" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> No hay catálogos disponibles para editar.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log("Formularios: Configurando eventos de pestañas...");

        // Manejar los botones de navegación entre formularios
        const formToggleButtons = document.querySelectorAll('.form-toggle-btn');
        const formSections = document.querySelectorAll('.form-section');

        formToggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                console.log("Botón toggle clickeado:", this.getAttribute('data-target'));

                // Remover clase activa de todos los botones
                formToggleButtons.forEach(btn => btn.classList.remove('active'));
                // Agregar clase activa al botón actual
                this.classList.add('active');

                // Ocultar todas las secciones de formularios
                formSections.forEach(section => section.style.display = 'none');

                // Mostrar la sección correspondiente
                const targetSection = document.getElementById(this.getAttribute('data-target'));
                if (targetSection) {
                    targetSection.style.display = 'block';
                }
            });
        });

        // Elementos del formulario de edición de producto
        const selectProducto = document.getElementById('select_producto');
        const editarProductoFormContainer = document.getElementById('editar_producto_form_container');
        const mensajeSeleccionProducto = document.getElementById('mensajeSeleccionProducto');
        const editarProductoId = document.getElementById('editar_producto_id');
        const editarCodigoProducto = document.getElementById('editar_codigo_producto');
        const editarNombreProducto = document.getElementById('editar_nombre_producto');
        const editarDescripcionProducto = document.getElementById('editar_descripcion_producto');
        const editarUnidadMedida = document.getElementById('editar_unidad_medida');
        const cancelarEdicionProducto = document.getElementById('cancelar_edicion_producto');

        // Elementos del formulario de edición de catálogo
        const selectCatalogo = document.getElementById('select_catalogo');
        const editarCatalogoFormContainer = document.getElementById('editar_catalogo_form_container');
        const mensajeSeleccionCatalogo = document.getElementById('mensajeSeleccionCatalogo');
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

                        // Mostrar el formulario y ocultar el mensaje
                        editarProductoFormContainer.style.display = 'block';
                        mensajeSeleccionProducto.style.display = 'none';
                    } else {
                        console.error('Error: No se encontraron datos para el producto seleccionado');
                    }
                } else {
                    // Ocultar el formulario y mostrar el mensaje
                    editarProductoFormContainer.style.display = 'none';
                    mensajeSeleccionProducto.style.display = 'block';
                }
            });
        }

        // Manejar el botón de cancelar edición de producto
        if (cancelarEdicionProducto) {
            cancelarEdicionProducto.addEventListener('click', function() {
                selectProducto.value = '';
                editarProductoFormContainer.style.display = 'none';
                mensajeSeleccionProducto.style.display = 'block';
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

                        // Mostrar el formulario y ocultar el mensaje
                        editarCatalogoFormContainer.style.display = 'block';
                        mensajeSeleccionCatalogo.style.display = 'none';
                    } else {
                        console.error('Error: No se encontraron datos para el catálogo seleccionado');
                    }
                } else {
                    // Ocultar el formulario y mostrar el mensaje
                    editarCatalogoFormContainer.style.display = 'none';
                    mensajeSeleccionCatalogo.style.display = 'block';
                }
            });
        }

        // Manejar el botón de cancelar edición de catálogo
        if (cancelarEdicionCatalogo) {
            cancelarEdicionCatalogo.addEventListener('click', function() {
                selectCatalogo.value = '';
                editarCatalogoFormContainer.style.display = 'none';
                mensajeSeleccionCatalogo.style.display = 'block';
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