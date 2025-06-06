<?php
if (!isset($pactos) || !isset($session) || !isset($productos)) {
    die("Error: No se han proporcionado las variables requeridas.");
}

// Array con los tipos de ubicación disponibles
$tiposUbicacion = ['Planta' => 'Planta', 'Botiquin' => 'Botiquín'];

// Obtener almacenes y botiquines
require_once(__DIR__ . '/../../controller/PlantaController.php');
require_once(__DIR__ . '/../../controller/BotiquinController.php');

use controller\PlantaController;
use controller\BotiquinController;

$plantaController = new PlantaController();
$botiquinController = new BotiquinController();

$plantas = $plantaController->index()['plantas'] ?? [];
$botiquines = $botiquinController->index()['botiquines'] ?? [];
?>

<div class="pactos-container">
    <div class="form-toggle-buttons mb-4">
        <button type="button" class="btn form-toggle-btn active" data-target="crearPactoForm">
            <i class="fas fa-handshake"></i> Crear Pacto
        </button>
        <button type="button" class="btn form-toggle-btn" data-target="editarPactoForm">
            <i class="fas fa-edit"></i> Editar Pacto
        </button>
    </div>

    <div class="row">
        <!-- Formulario para crear pacto -->
        <div class="col-md-12 form-section" id="crearPactoForm">
            <div class="card shadow mb-4">
                <form id="formCrearPacto" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/PactosController.php" method="POST" class="card-form">
                    <input type="hidden" name="action" value="crear">
                    
                    <div class="row">
                        <div class="form-group mb-3">
                            <label for="producto_pacto" class="form-label">Producto <span class="text-danger">*</span></label>
                            <select class="form-select" id="producto_pacto" name="id_producto" required>
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
                            <label for="tipo_ubicacion" class="form-label">Tipo de Ubicación <span class="text-danger">*</span></label>
                            <select class="form-select" id="tipo_ubicacion" name="tipo_ubicacion" required>
                                <option value="">Seleccione un tipo de ubicación</option>
                                <?php foreach ($tiposUbicacion as $key => $value): ?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="form-group mb-3">
                            <label for="destino_pacto" class="form-label">Destino <span class="text-danger">*</span></label>
                            <select class="form-select" id="destino_pacto" name="id_destino" required disabled>
                                <option value="">Primero seleccione un tipo de ubicación</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="form-group mb-3">
                            <label for="cantidad_pactada" class="form-label">Cantidad Pactada <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="cantidad_pactada" name="cantidad_pactada" min="1" required>
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
        
        <!-- Formulario para editar pacto -->
        <div class="col-md-12 form-section" id="editarPactoForm" style="display: none;">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <?php if (count($pactos) > 0): ?>
                        <!-- Selector de pacto -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="select_pacto" class="form-label">Seleccione un pacto</label>
                                    <select class="form-select" id="select_pacto">
                                        <option value="">Seleccione un pacto para editar</option>
                                        <?php foreach ($pactos as $pacto): 
                                            $nombreProducto = "";
                                            foreach ($productos as $producto) {
                                                if ($producto->getIdProducto() == $pacto->getIdProducto()) {
                                                    $nombreProducto = $producto->getNombre();
                                                    break;
                                                }
                                            }
                                            $tipoUbicacion = $pacto->getTipoUbicacion();
                                            
                                            // Obtener nombre del destino
                                            $nombreDestino = "";
                                            if ($tipoUbicacion === 'Planta') {
                                                foreach ($plantas as $planta) {
                                                    if ($planta->getIdPlanta() == $pacto->getIdDestino()) {
                                                        $nombreDestino = $planta->getNombre();
                                                        break;
                                                    }
                                                }
                                            } else if ($tipoUbicacion === 'Botiquin') {
                                                foreach ($botiquines as $botiquin) {
                                                    if ($botiquin->getIdBotiquin() == $pacto->getIdDestino()) {
                                                        $nombreDestino = $botiquin->getNombre();
                                                        break;
                                                    }
                                                }
                                            }
                                        ?>
                                            <option value="<?= $pacto->getIdPacto() ?>">
                                                <?= htmlspecialchars($nombreProducto) ?> - <?= $tipoUbicacion ?>: <?= htmlspecialchars($nombreDestino) ?> - Cant: <?= $pacto->getCantidadPactada() ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Seleccione un pacto para editar sus datos</div>
                                </div>
                            </div>
                        </div>

                        <!-- Mensaje de selección inicial -->
                        <div id="mensajeSeleccionPacto" class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i> Seleccione un pacto para editar
                        </div>
                    
                        <!-- Formulario de edición -->
                        <div id="editar_pacto_form_container" style="display: none;">
                            <form id="formEditarPacto" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/PactosController.php" method="POST" class="card-form">
                                <input type="hidden" name="action" value="editar">
                                <input type="hidden" id="editar_pacto_id" name="id">
                                
                                <div class="row">
                                    <div class="form-group mb-3">
                                        <label for="editar_producto_pacto" class="form-label">Producto <span class="text-danger">*</span></label>
                                        <select class="form-select" id="editar_producto_pacto" name="id_producto" required>
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
                                        <label for="editar_tipo_ubicacion" class="form-label">Tipo de Ubicación <span class="text-danger">*</span></label>
                                        <select class="form-select" id="editar_tipo_ubicacion" name="tipo_ubicacion" required>
                                            <option value="">Seleccione un tipo de ubicación</option>
                                            <?php foreach ($tiposUbicacion as $key => $value): ?>
                                                <option value="<?= $key ?>"><?= $value ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group mb-3">
                                        <label for="editar_destino_pacto" class="form-label">Destino <span class="text-danger">*</span></label>
                                        <select class="form-select" id="editar_destino_pacto" name="id_destino" required>
                                            <option value="">Primero seleccione un tipo de ubicación</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group mb-3">
                                        <label for="editar_cantidad_pactada" class="form-label">Cantidad Pactada <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="editar_cantidad_pactada" name="cantidad_pactada" min="1" required>
                                    </div>
                                </div>
                                
                                <div class="form-group mt-4 text-center">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Actualizar
                                    </button>
                                    <button type="button" id="cancelar_edicion_pacto" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> No hay pactos disponibles para editar.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inicializacion de datos para los pactos -->
<script>
// Datos para cargar los destinos según el tipo de ubicación
const plantas = <?= json_encode(array_map(function($p) { 
    return ['id' => $p->getIdPlanta(), 'nombre' => $p->getNombre()]; 
}, $plantas)) ?>;

const botiquines = <?= json_encode(array_map(function($b) { 
    return ['id' => $b->getIdBotiquin(), 'nombre' => $b->getNombre()]; 
}, $botiquines)) ?>;

// Datos de pactos para edición
window.pactosDataObj = {};
<?php foreach ($pactos as $pacto): ?>
window.pactosDataObj[<?= $pacto->getIdPacto() ?>] = {
    id: <?= $pacto->getIdPacto() ?>,
    id_producto: <?= $pacto->getIdProducto() ?>,
    tipo_ubicacion: '<?= $pacto->getTipoUbicacion() ?>',
    id_destino: <?= $pacto->getIdDestino() ?>,
    cantidad_pactada: <?= $pacto->getCantidadPactada() ?>
};
<?php endforeach; ?>

document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario de creación
    const tipoUbicacion = document.getElementById('tipo_ubicacion');
    const destinoPacto = document.getElementById('destino_pacto');
    
    // Elementos del formulario de edición
    const selectPacto = document.getElementById('select_pacto');
    const editarPactoFormContainer = document.getElementById('editar_pacto_form_container');
    const mensajeSeleccionPacto = document.getElementById('mensajeSeleccionPacto');
    const editarPactoId = document.getElementById('editar_pacto_id');
    const editarProductoPacto = document.getElementById('editar_producto_pacto');
    const editarTipoUbicacion = document.getElementById('editar_tipo_ubicacion');
    const editarDestinoPacto = document.getElementById('editar_destino_pacto');
    const editarCantidadPactada = document.getElementById('editar_cantidad_pactada');
    const cancelarEdicionPacto = document.getElementById('cancelar_edicion_pacto');
    
    // Configuración de la alternancia entre formularios
    const formToggleButtons = document.querySelectorAll('.form-toggle-btn');
    formToggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Desactivar todos los botones
            formToggleButtons.forEach(btn => btn.classList.remove('active'));
            // Activar este botón
            this.classList.add('active');
            
            // Ocultar todos los formularios
            document.querySelectorAll('.form-section').forEach(form => {
                form.style.display = 'none';
            });
            
            // Mostrar el formulario correspondiente
            const targetForm = document.getElementById(this.getAttribute('data-target'));
            if (targetForm) {
                targetForm.style.display = 'block';
            }
        });
    });
    
    // Función para cargar los destinos según el tipo de ubicación seleccionado
    function cargarDestinos(tipoSelect, destinoSelect) {
        const tipoSeleccionado = tipoSelect.value;
        destinoSelect.innerHTML = '<option value="">Seleccione un destino</option>';
        
        if (tipoSeleccionado) {
            destinoSelect.disabled = false;
            let destinos = [];
            
            if (tipoSeleccionado === 'Planta') {
                destinos = plantas;
            } else if (tipoSeleccionado === 'Botiquin') {
                destinos = botiquines;
            }
            
            destinos.forEach(destino => {
                const option = document.createElement('option');
                option.value = destino.id;
                option.textContent = destino.nombre;
                destinoSelect.appendChild(option);
            });
        } else {
            destinoSelect.disabled = true;
        }
    }
    
    // Manejar cambios en el tipo de ubicación
    if (tipoUbicacion) {
        tipoUbicacion.addEventListener('change', function() {
            cargarDestinos(tipoUbicacion, destinoPacto);
        });
    }
    
    if (editarTipoUbicacion) {
        editarTipoUbicacion.addEventListener('change', function() {
            cargarDestinos(editarTipoUbicacion, editarDestinoPacto);
        });
    }
    
    // Manejar la selección de un pacto para editar
    if (selectPacto) {
        selectPacto.addEventListener('change', function() {
            const pactoId = this.value;
            
            if (pactoId) {
                const pacto = window.pactosDataObj[pactoId];
                if (pacto) {
                    // Llenar el formulario con los datos del pacto
                    editarPactoId.value = pacto.id;
                    editarProductoPacto.value = pacto.id_producto;
                    editarTipoUbicacion.value = pacto.tipo_ubicacion;
                    editarCantidadPactada.value = pacto.cantidad_pactada;
                    
                    // Cargar los destinos según el tipo de ubicación
                    cargarDestinos(editarTipoUbicacion, editarDestinoPacto);
                    
                    // Esperar un poco para asegurar que los destinos se hayan cargado
                    setTimeout(() => {
                        editarDestinoPacto.value = pacto.id_destino;
                    }, 100);
                    
                    // Mostrar el formulario y ocultar el placeholder
                    editarPactoFormContainer.style.display = 'block';
                    mensajeSeleccionPacto.style.display = 'none';
                } else {
                    console.error('Error: No se encontraron datos para el pacto seleccionado');
                }
            } else {
                // Ocultar el formulario y mostrar el placeholder
                editarPactoFormContainer.style.display = 'none';
                mensajeSeleccionPacto.style.display = 'block';
            }
        });
    }
    
    // Manejar el botón de cancelar edición
    if (cancelarEdicionPacto) {
        cancelarEdicionPacto.addEventListener('click', function() {
            selectPacto.value = '';
            editarPactoFormContainer.style.display = 'none';
            mensajeSeleccionPacto.style.display = 'block';
        });
    }
});
</script>
