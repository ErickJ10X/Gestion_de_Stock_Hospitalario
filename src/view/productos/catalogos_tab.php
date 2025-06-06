<?php
if (!isset($catalogos) || !isset($plantas) || !isset($productos) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="list-header__actions">
    <button id="btn-add-catalogo" class="list-button list-button--success" onclick="document.querySelector('.tab-btn[data-tab=\'tab-agregar-editar\']').click()">
        <i class="bi bi-plus-circle"></i> Nuevo Catálogo
    </button>
</div>

<div class="table-responsive">
    <table class="list-table" id="tablaCatalogos">
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Código</th>
                <th>Planta</th>
                <th>Hospital</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($catalogos)): ?>
                <tr>
                    <td colspan="7" class="list-table__empty">No hay catálogos registrados</td>
                </tr>
            <?php else: ?>
                <?php foreach ($catalogos as $catalogo): 
                    // Buscar producto asociado
                    $producto = null;
                    foreach ($productos as $p) {
                        if ($p->getIdProducto() == $catalogo->getIdProducto()) {
                            $producto = $p;
                            break;
                        }
                    }
                    
                    // Buscar planta asociada
                    $planta = null;
                    $hospital = null;
                    foreach ($plantas as $p) {
                        if ($p->getIdPlanta() == $catalogo->getIdPlanta()) {
                            $planta = $p;
                            // Si hay hospital asociado a la planta, obtener su nombre
                            if ($planta->getHospital()) {
                                $hospital = $planta->getHospital()->getNombre();
                            }
                            break;
                        }
                    }
                    
                    if (!$producto || !$planta) continue;
                ?>
                    <tr class="list-table__body-row <?= $catalogo->isActivo() ? '' : 'text-muted' ?>">
                        <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($catalogo->getIdCatalogo()) ?></td>
                        <td class="list-table__body-cell" data-label="Producto"><?= htmlspecialchars($producto->getNombre()) ?></td>
                        <td class="list-table__body-cell" data-label="Código"><?= htmlspecialchars($producto->getCodigo()) ?></td>
                        <td class="list-table__body-cell" data-label="Planta"><?= htmlspecialchars($planta->getNombre()) ?></td>
                        <td class="list-table__body-cell" data-label="Hospital"><?= $hospital ? htmlspecialchars($hospital) : 'N/A' ?></td>
                        <td class="list-table__body-cell" data-label="Estado">
                            <?php if ($catalogo->isActivo()): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="list-table__body-cell" data-label="Acciones">
                            <div class="list-table__actions">
                                <button class="list-table__button list-table__button--edit" 
                                        onclick="seleccionarCatalogo(<?= $catalogo->getIdCatalogo() ?>)"
                                        title="Editar catálogo">
                                    <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                </button>
                                <button class="list-table__button list-table__button--delete"
                                        onclick="confirmarEliminarCatalogo(<?= $catalogo->getIdCatalogo() ?>, '<?= htmlspecialchars(addslashes($producto->getNombre())) ?>', '<?= htmlspecialchars(addslashes($planta->getNombre())) ?>')"
                                        title="Eliminar del catálogo">
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

<!-- Modal para confirmar eliminación -->
<div class="modal fade" id="eliminarCatalogoModal" tabindex="-1" aria-labelledby="eliminarCatalogoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="eliminarCatalogoModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar del catálogo el producto <strong id="nombreProductoCatalogo"></strong> de la planta <strong id="nombrePlantaCatalogo"></strong>?</p>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/CatalogosController.php" method="POST">
                    <input type="hidden" name="action" value="eliminar">
                    <input type="hidden" name="id" id="id_catalogo_eliminar">
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
            $('#tablaCatalogos').DataTable({
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

// Función para seleccionar un catálogo para editar
function seleccionarCatalogo(id) {
    // Cambiar a la pestaña de edición
    document.querySelector('.tab-btn[data-tab="tab-agregar-editar"]').click();
    
    // Esperar un momento para que la pestaña se muestre
    setTimeout(() => {
        // Asegurarse de que el formulario de catálogos esté visible
        const catalogosSection = document.getElementById('catalogosSection');
        if (catalogosSection) {
            catalogosSection.scrollIntoView({
                behavior: 'smooth'
            });
        }
        
        // Seleccionar el catálogo en el dropdown
        const selectCatalogo = document.getElementById('select_catalogo');
        if (selectCatalogo) {
            selectCatalogo.value = id;
            
            // Disparar el evento change para cargar los datos
            const event = new Event('change');
            selectCatalogo.dispatchEvent(event);
        }
    }, 100);
}

// Función para confirmar eliminación de un catálogo
function confirmarEliminarCatalogo(id, nombreProducto, nombrePlanta) {
    document.getElementById('id_catalogo_eliminar').value = id;
    document.getElementById('nombreProductoCatalogo').textContent = nombreProducto;
    document.getElementById('nombrePlantaCatalogo').textContent = nombrePlanta;
    
    // Mostrar el modal
    const modalEliminar = new bootstrap.Modal(document.getElementById('eliminarCatalogoModal'));
    modalEliminar.show();
}
</script>
