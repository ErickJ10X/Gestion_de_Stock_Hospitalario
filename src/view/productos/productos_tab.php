<?php
if (!isset($productos) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="table-responsive">
    <table class="list-table" id="tablaProductos">
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

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="eliminarProductoModal" tabindex="-1" aria-labelledby="eliminarProductoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="eliminarProductoModalLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar el producto?</p>
                <p id="nombreProductoEliminar" class="fw-bold"></p>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/ProductoHandler.php" method="POST">
                    <input type="hidden" name="action" value="eliminar">
                    <input type="hidden" id="id_producto_eliminar" name="id">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTable si está disponible
    if (typeof $.fn.DataTable !== 'undefined') {
        try {
            $('#tablaProductos').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                },
                responsive: true,
                order: [[0, 'asc']]
            });
        } catch (error) {
            console.error('Error al inicializar DataTable:', error);
        }
    }
});

// Función para seleccionar un producto para editar
function seleccionarProducto(id) {
    // Cambiar a la pestaña de edición
    document.querySelector('.tab-btn[data-tab="tab-agregar-editar"]').click();
    
    // Esperar un momento para que la pestaña se muestre
    setTimeout(() => {
        // Seleccionar el producto en el dropdown
        const selectProducto = document.getElementById('select_producto');
        selectProducto.value = id;
        
        // Disparar el evento change para cargar los datos
        const event = new Event('change');
        selectProducto.dispatchEvent(event);
        
        // Hacer scroll al formulario de edición
        document.getElementById('editar_producto_form_container').scrollIntoView({
            behavior: 'smooth'
        });
    }, 100);
}

// Función para confirmar eliminación de un producto
function confirmarEliminarProducto(id, nombre) {
    document.getElementById('id_producto_eliminar').value = id;
    document.getElementById('nombreProductoEliminar').textContent = nombre;
    
    // Mostrar el modal
    const modalEliminar = new bootstrap.Modal(document.getElementById('eliminarProductoModal'));
    modalEliminar.show();
}
</script>
