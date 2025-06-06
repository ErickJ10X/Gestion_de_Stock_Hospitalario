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

<!-- Tarjeta principal de la tabla -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <button id="btnNuevoPacto" class="btn btn-sm btn-success">
            <i class="fas fa-plus-circle me-1"></i> Nuevo Pacto
        </button>

        <div class="search-group">
            <div class="input-group input-group-sm">
                <select id="registrosPorPaginaPactos" class="form-select form-select-sm">
                    <option value="5">5 registros</option>
                    <option value="10" selected>10 registros</option>
                    <option value="25">25 registros</option>
                    <option value="50">50 registros</option>
                </select>
            </div>
            <div class="input-group input-group-sm">
                <input type="text" id="buscarPacto" class="form-control" placeholder="Buscar...">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="list-table table table-striped table-hover" id="pactosDataTable">
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
                            <td class="list-table__body-cell" data-label="ID">
                                <?= htmlspecialchars($pacto->getIdPacto()) ?>
                            </td>
                            <td class="list-table__body-cell" data-label="Producto">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-box me-2 text-primary"></i>
                                    <span><?= htmlspecialchars(getProductoNombreById($pacto->getIdProducto(), $productos)) ?></span>
                                </div>
                            </td>
                            <td class="list-table__body-cell" data-label="Tipo Ubicación">
                                <?php if ($pacto->getTipoUbicacion() === 'Planta'): ?>
                                    <span class="badge bg-info">Planta</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Botiquín</span>
                                <?php endif; ?>
                            </td>
                            <td class="list-table__body-cell" data-label="Destino">
                                <?= htmlspecialchars(getDestinoNombre($pacto->getIdDestino(), $pacto->getTipoUbicacion(), $plantas, $botiquines)) ?>
                            </td>
                            <td class="list-table__body-cell" data-label="Cantidad Pactada">
                                <span class="badge bg-primary"><?= htmlspecialchars($pacto->getCantidadPactada()) ?></span>
                            </td>
                            <td class="list-table__body-cell" data-label="Acciones">
                                <div class="list-table__actions">
                                    <button class="list-table__button list-table__button--edit btn-edit-pacto"
                                            data-id="<?= $pacto->getIdPacto() ?>">
                                        <i class="bi bi-pencil-square list-table__button-icon"></i> Editar
                                    </button>
                                    <button class="list-table__button list-table__button--delete btn-delete-pacto"
                                            data-id="<?= $pacto->getIdPacto() ?>">
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

        <!-- Paginación con JS -->
        <div class="card-footer bg-white py-3">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <div class="paginacion-info">
                        Mostrando <span id="inicio-registros-pactos">1</span> a <span id="fin-registros-pactos">10</span>
                        de <span id="total-registros-pactos"><?= count($pactos) ?></span> registros
                    </div>
                </div>
                <div class="col-md-7">
                    <nav aria-label="Paginación de pactos">
                        <ul class="pagination justify-content-end mb-0" id="paginacion-pactos">
                            <!-- La paginación se generará dinámicamente con JavaScript -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para confirmar eliminación de pacto -->
<div class="modal fade" id="eliminarPactoModal" tabindex="-1" aria-labelledby="eliminarPactoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="eliminarPactoModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar este pacto?</p>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/PactosController.php" method="POST">
                    <input type="hidden" name="action" value="eliminar">
                    <input type="hidden" name="id" id="id_pacto_eliminar">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Inicializar datos de pactos para paginación
    window.datosPactos = <?= json_encode(array_map(function ($p) use ($productos, $plantas, $botiquines) {
        $nombreProducto = "";
        foreach ($productos as $producto) {
            if ($producto->getIdProducto() == $p->getIdProducto()) {
                $nombreProducto = $producto->getNombre();
                break;
            }
        }
        
        $nombreDestino = "";
        $tipoUbicacion = $p->getTipoUbicacion();
        if ($tipoUbicacion === 'Planta') {
            foreach ($plantas as $planta) {
                if ($planta->getIdPlanta() == $p->getIdDestino()) {
                    $nombreDestino = $planta->getNombre();
                    break;
                }
            }
        } else if ($tipoUbicacion === 'Botiquin') {
            foreach ($botiquines as $botiquin) {
                if ($botiquin->getIdBotiquin() == $p->getIdDestino()) {
                    $nombreDestino = $botiquin->getNombre();
                    break;
                }
            }
        }
        
        return [
            'id' => $p->getIdPacto(),
            'id_producto' => $p->getIdProducto(),
            'producto_nombre' => $nombreProducto,
            'tipo_ubicacion' => $tipoUbicacion,
            'id_destino' => $p->getIdDestino(),
            'destino_nombre' => $nombreDestino,
            'cantidad_pactada' => $p->getCantidadPactada()
        ];
    }, $pactos)) ?>;
    
    // Total de registros para paginación
    window.totalRegistrosPactos = <?= count($pactos) ?>;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Manejar botón nuevo pacto
        const btnNuevoPacto = document.getElementById('btnNuevoPacto');
        if (btnNuevoPacto) {
            btnNuevoPacto.addEventListener('click', function() {
                // Cambiar a la pestaña de agregar/editar
                document.querySelector('.tab-btn[data-tab="tab-agregar-editar"]').click();
                
                // Asegurarse que el formulario de crear pacto esté activo
                setTimeout(() => {
                    const crearPactoBtn = document.querySelector('.form-toggle-btn[data-target="crearPactoForm"]');
                    if (crearPactoBtn && !crearPactoBtn.classList.contains('active')) {
                        crearPactoBtn.click();
                    }
                }, 100);
            });
        }
        
        // Manejar botón editar pacto
        const btnEditPactos = document.querySelectorAll('.btn-edit-pacto');
        if (btnEditPactos) {
            btnEditPactos.forEach(btn => {
                btn.addEventListener('click', function() {
                    const pactoId = this.getAttribute('data-id');
                    
                    // Cambiar a la pestaña de agregar/editar
                    document.querySelector('.tab-btn[data-tab="tab-agregar-editar"]').click();
                    
                    // Asegurarse que el formulario de editar pacto esté activo
                    setTimeout(() => {
                        const editarPactoBtn = document.querySelector('.form-toggle-btn[data-target="editarPactoForm"]');
                        if (editarPactoBtn && !editarPactoBtn.classList.contains('active')) {
                            editarPactoBtn.click();
                        }
                        
                        // Seleccionar el pacto para editar
                        setTimeout(() => {
                            const selectPacto = document.getElementById('select_pacto');
                            if (selectPacto) {
                                selectPacto.value = pactoId;
                                
                                // Disparar evento change para cargar los datos
                                const changeEvent = new Event('change');
                                selectPacto.dispatchEvent(changeEvent);
                            }
                        }, 100);
                    }, 100);
                });
            });
        }
        
        // Manejar botón eliminar pacto
        const btnDeletePactos = document.querySelectorAll('.btn-delete-pacto');
        if (btnDeletePactos) {
            btnDeletePactos.forEach(btn => {
                btn.addEventListener('click', function() {
                    const pactoId = this.getAttribute('data-id');
                    document.getElementById('id_pacto_eliminar').value = pactoId;
                    
                    // Mostrar modal de confirmación
                    const eliminarPactoModal = new bootstrap.Modal(document.getElementById('eliminarPactoModal'));
                    eliminarPactoModal.show();
                });
            });
        }
    });
</script>
