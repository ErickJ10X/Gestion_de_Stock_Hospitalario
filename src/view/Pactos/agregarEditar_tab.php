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

<div class="row">
    <!-- Formulario para agregar pacto -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="section-title bg-primary text-white">
                <i class="fas fa-handshake me-2"></i> Crear Pacto
            </div>
            <div class="card-body">
                <form id="formCrearPacto" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/PactosController.php" method="POST" class="card-form">
                    <input type="hidden" name="action" value="crear">
                    
                    <div class="mb-3">
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
                    
                    <div class="mb-3">
                        <label for="tipo_ubicacion" class="form-label">Tipo de Ubicación <span class="text-danger">*</span></label>
                        <select class="form-select" id="tipo_ubicacion" name="tipo_ubicacion" required>
                            <option value="">Seleccione un tipo de ubicación</option>
                            <?php foreach ($tiposUbicacion as $key => $value): ?>
                                <option value="<?= $key ?>"><?= $value ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="destino_pacto" class="form-label">Destino <span class="text-danger">*</span></label>
                        <select class="form-select" id="destino_pacto" name="id_destino" required disabled>
                            <option value="">Primero seleccione un tipo de ubicación</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cantidad_pactada" class="form-label">Cantidad Pactada <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="cantidad_pactada" name="cantidad_pactada" min="1" required>
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
    
    <!-- Formulario para editar pacto -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="section-title bg-warning text-dark">
                <i class="fas fa-edit me-2"></i> Editar Pacto
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="select_pacto" class="form-label">Seleccionar Pacto</label>
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
                        ?>
                            <option value="<?= $pacto->getIdPacto() ?>">
                                <?= htmlspecialchars($nombreProducto) ?> - <?= $tipoUbicacion ?> - Cant: <?= $pacto->getCantidadPactada() ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div id="editar_pacto_form_container" style="display: none;">
                    <form id="formEditarPacto" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/PactosController.php" method="POST" class="card-form">
                        <input type="hidden" name="action" value="editar">
                        <input type="hidden" id="editar_pacto_id" name="id">
                        
                        <div class="mb-3">
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
                        
                        <div class="mb-3">
                            <label for="editar_tipo_ubicacion" class="form-label">Tipo de Ubicación <span class="text-danger">*</span></label>
                            <select class="form-select" id="editar_tipo_ubicacion" name="tipo_ubicacion" required>
                                <option value="">Seleccione un tipo de ubicación</option>
                                <?php foreach ($tiposUbicacion as $key => $value): ?>
                                    <option value="<?= $key ?>"><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editar_destino_pacto" class="form-label">Destino <span class="text-danger">*</span></label>
                            <select class="form-select" id="editar_destino_pacto" name="id_destino" required>
                                <option value="">Primero seleccione un tipo de ubicación</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editar_cantidad_pactada" class="form-label">Cantidad Pactada <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="editar_cantidad_pactada" name="cantidad_pactada" min="1" required>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" id="cancelar_edicion_pacto" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar
                            </button>
                        </div>
                    </form>
                </div>
                
                <div id="editar_pacto_placeholder" class="text-center text-muted py-4">
                    <i class="fas fa-handshake fa-3x mb-3"></i>
                    <p>Seleccione un pacto para editarlo</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario de creación
    const tipoUbicacion = document.getElementById('tipo_ubicacion');
    const destinoPacto = document.getElementById('destino_pacto');
    
    // Elementos del formulario de edición
    const selectPacto = document.getElementById('select_pacto');
    const editarPactoFormContainer = document.getElementById('editar_pacto_form_container');
    const editarPactoPlaceholder = document.getElementById('editar_pacto_placeholder');
    const editarPactoId = document.getElementById('editar_pacto_id');
    const editarProductoPacto = document.getElementById('editar_producto_pacto');
    const editarTipoUbicacion = document.getElementById('editar_tipo_ubicacion');
    const editarDestinoPacto = document.getElementById('editar_destino_pacto');
    const editarCantidadPactada = document.getElementById('editar_cantidad_pactada');
    const cancelarEdicionPacto = document.getElementById('cancelar_edicion_pacto');
    
    // Datos para cargar los destinos según el tipo de ubicación
    const plantas = <?= json_encode(array_map(function($p) { 
        return ['id' => $p->getIdPlanta(), 'nombre' => $p->getNombre()]; 
    }, $plantas)) ?>;
    
    const botiquines = <?= json_encode(array_map(function($b) { 
        return ['id' => $b->getIdBotiquin(), 'nombre' => $b->getNombre()]; 
    }, $botiquines)) ?>;
    
    // Datos de pactos para edición
    const pactos = {};
    <?php foreach ($pactos as $pacto): ?>
        pactos[<?= $pacto->getIdPacto() ?>] = {
            id: <?= $pacto->getIdPacto() ?>,
            id_producto: <?= $pacto->getIdProducto() ?>,
            tipo_ubicacion: '<?= $pacto->getTipoUbicacion() ?>',
            id_destino: <?= $pacto->getIdDestino() ?>,
            cantidad_pactada: <?= $pacto->getCantidadPactada() ?>
        };
    <?php endforeach; ?>
    
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
    tipoUbicacion.addEventListener('change', function() {
        cargarDestinos(tipoUbicacion, destinoPacto);
    });
    
    editarTipoUbicacion.addEventListener('change', function() {
        cargarDestinos(editarTipoUbicacion, editarDestinoPacto);
    });
    
    // Manejar la selección de un pacto para editar
    selectPacto.addEventListener('change', function() {
        const pactoId = this.value;
        
        if (pactoId) {
            const pacto = pactos[pactoId];
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
                editarPactoPlaceholder.style.display = 'none';
            } else {
                console.error('Error: No se encontraron datos para el pacto seleccionado');
            }
        } else {
            // Ocultar el formulario y mostrar el placeholder
            editarPactoFormContainer.style.display = 'none';
            editarPactoPlaceholder.style.display = 'block';
        }
    });
    
    // Manejar el botón de cancelar edición
    cancelarEdicionPacto.addEventListener('click', function() {
        selectPacto.value = '';
        editarPactoFormContainer.style.display = 'none';
        editarPactoPlaceholder.style.display = 'block';
    });
});
</script>
