<?php
if (!isset($pactos) || !isset($session) || !isset($productos)) {
    die("Error: No se han proporcionado las variables requeridas.");
}

// Obtener datos adicionales necesarios
require_once(__DIR__ . '/../../controller/PlantaController.php');
require_once(__DIR__ . '/../../controller/BotiquinController.php');

use controller\PlantaController;
use controller\BotiquinController;

$plantaController = new PlantaController();
$botiquinController = new BotiquinController();

$plantas = $plantaController->index()['plantas'] ?? [];
$botiquines = $botiquinController->index()['botiquines'] ?? [];

// Función para obtener el nombre del producto por ID
function getProductoNombreById($idProducto, $productos) {
    foreach ($productos as $producto) {
        if ($producto->getIdProducto() == $idProducto) {
            return $producto->getNombre();
        }
    }
    return "Producto no encontrado";
}

// Función para obtener el nombre del destino según tipo y ID
function getDestinoNombre($idDestino, $tipoUbicacion, $plantas, $botiquines) {
    if ($tipoUbicacion === 'Planta') {
        foreach ($plantas as $planta) {
            if ($planta->getIdPlanta() == $idDestino) {
                return $planta->getNombre();
            }
        }
    } 
    else if ($tipoUbicacion === 'Botiquin') {
        foreach ($botiquines as $botiquin) {
            if ($botiquin->getIdBotiquin() == $idDestino) {
                return $botiquin->getNombre();
            }
        }
    }
    return "Destino no encontrado";
}
?>

<div class="list-header__actions">
    <button id="btn-add-pacto" class="list-button list-button--success" onclick="document.querySelector('.tab-btn[data-tab=\'tab-agregar-editar\']').click()">
        <i class="bi bi-plus-circle"></i> Nuevo Pacto
    </button>
</div>

<div class="table-responsive">
    <table class="list-table" id="tablaPactos">
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
                <?php foreach ($pactos as $pacto): ?>
                    <tr class="list-table__body-row">
                        <td class="list-table__body-cell" data-label="ID"><?= htmlspecialchars($pacto->getIdPacto()) ?></td>
                        <td class="list-table__body-cell" data-label="Producto">
                            <?= htmlspecialchars(getProductoNombreById($pacto->getIdProducto(), $productos)) ?>
                        </td>
                        <td class="list-table__body-cell" data-label="Tipo Ubicación">
                            <?= htmlspecialchars($pacto->getTipoUbicacion()) ?>
                        </td>
                        <td class="list-table__body-cell" data-label="Destino">
                            <?= htmlspecialchars(getDestinoNombre($pacto->getIdDestino(), $pacto->getTipoUbicacion(), $plantas, $botiquines)) ?>
                        </td>
                        <td class="list-table__body-cell" data-label="Cantidad Pactada">
                            <?= htmlspecialchars($pacto->getCantidadPactada()) ?>
                        </td>
                        <td class="list-table__body-cell" data-label="Acciones">
                            <div class="list-table__actions">
                                <button class="list-table__button list-table__button--edit"
                                        onclick="seleccionarPacto(<?= $pacto->getIdPacto() ?>)"
                                        title="Editar pacto">
                                    <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                </button>
                                <button class="list-table__button list-table__button--delete"
                                        onclick="confirmarEliminarPacto(<?= $pacto->getIdPacto() ?>)"
                                        title="Eliminar pacto">
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
<div class="modal fade" id="eliminarPactoModal" tabindex="-1" aria-labelledby="eliminarPactoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="eliminarPactoModalLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar este pacto?</p>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/PactosController.php" method="POST">
                    <input type="hidden" name="action" value="eliminar">
                    <input type="hidden" id="id_pacto_eliminar" name="id">
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
            $('#tablaPactos').DataTable({
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

// Función para seleccionar un pacto para editar
function seleccionarPacto(id) {
    // Cambiar a la pestaña de edición
    document.querySelector('.tab-btn[data-tab="tab-agregar-editar"]').click();
    
    // Esperar un momento para que la pestaña se muestre
    setTimeout(() => {
        // Seleccionar el pacto en el dropdown
        const selectPacto = document.getElementById('select_pacto');
        selectPacto.value = id;
        
        // Disparar el evento change para cargar los datos
        const event = new Event('change');
        selectPacto.dispatchEvent(event);
        
        // Hacer scroll al formulario de edición
        document.getElementById('editar_pacto_form_container').scrollIntoView({
            behavior: 'smooth'
        });
    }, 100);
}

// Función para confirmar eliminación de un pacto
function confirmarEliminarPacto(id) {
    document.getElementById('id_pacto_eliminar').value = id;
    
    // Mostrar el modal
    const modalEliminar = new bootstrap.Modal(document.getElementById('eliminarPactoModal'));
    modalEliminar.show();
}
</script>
