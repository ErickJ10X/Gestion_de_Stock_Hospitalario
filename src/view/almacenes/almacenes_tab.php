<?php
if (!isset($almacenes) || !isset($session) || !isset($plantas) || !isset($hospitales)) {
    die("Error: No se han proporcionado las variables requeridas.");
}

// Crear mapeos para mostrar nombres en lugar de IDs
$plantasMap = [];
foreach ($plantas as $planta) {
    $plantasMap[$planta->getIdPlanta()] = $planta->getNombre();
}

$hospitalesMap = [];
foreach ($hospitales as $hospital) {
    $hospitalesMap[$hospital->getIdHospital()] = $hospital->getNombre();
}
?>

<div class="list-header__actions">
    <button id="btn-add-almacen" class="list-button list-button--success" onclick="document.querySelector('.tab-btn[data-tab=\'tab-agregar-editar\']').click()">
        <i class="bi bi-plus-circle"></i> Nuevo
    </button>
</div>

<div class="table-responsive">
    <table class="list-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Planta</th>
                <th>Hospital</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($almacenes)): ?>
                <tr>
                    <td colspan="5" class="list-table__empty">No hay almacenes registrados</td>
                </tr>
            <?php else: ?>
                <?php foreach ($almacenes as $almacen): ?>
                    <tr class="list-table__body-row">
                        <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($almacen->getIdAlmacen()) ?></td>
                        <td class="list-table__body-cell" data-label="Tipo"><?= htmlspecialchars($almacen->getTipo()) ?></td>
                        <td class="list-table__body-cell" data-label="Planta">
                            <?= $almacen->getIdPlanta() ? htmlspecialchars($plantasMap[$almacen->getIdPlanta()] ?? 'Sin nombre') : 'N/A' ?>
                        </td>
                        <td class="list-table__body-cell" data-label="Hospital">
                            <?= htmlspecialchars($hospitalesMap[$almacen->getIdHospital()] ?? 'Sin nombre') ?>
                        </td>
                        <td class="list-table__body-cell" data-label="Acciones">
                            <div class="list-table__actions">
                                <button class="list-table__button list-table__button--edit btn-edit-almacen" 
                                        onclick="editarAlmacen(<?= $almacen->getIdAlmacen() ?>)">
                                    <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                </button>
                                <button class="list-table__button list-table__button--delete btn-delete-almacen"
                                        onclick="eliminarAlmacen(<?= $almacen->getIdAlmacen() ?>)">
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

<!-- Modal para eliminar almacén -->
<div class="modal fade" id="eliminarAlmacenModal" tabindex="-1" aria-labelledby="eliminarAlmacenModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eliminarAlmacenModalLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea eliminar este almacén?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/AlmacenesController.php" method="post">
                    <input type="hidden" name="action" value="eliminar">
                    <input type="hidden" name="id" id="eliminar_almacen_id">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editarAlmacen(id) {
    // Cambiar a la pestaña de agregar/editar
    document.querySelector('.tab-btn[data-tab="tab-agregar-editar"]').click();
    
    // Seleccionar el almacén en el dropdown
    const selectAlmacen = document.getElementById('select_almacen');
    selectAlmacen.value = id;
    
    // Disparar el evento change para cargar los datos
    const event = new Event('change');
    selectAlmacen.dispatchEvent(event);
}

function eliminarAlmacen(id) {
    // Configurar el modal
    document.getElementById('eliminar_almacen_id').value = id;
    
    // Mostrar el modal usando Bootstrap
    var myModal = new bootstrap.Modal(document.getElementById('eliminarAlmacenModal'));
    myModal.show();
}
</script>
