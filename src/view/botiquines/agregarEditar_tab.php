<?php
if (!isset($hospitales) || !isset($plantaController) || !isset($plantas) || !isset($botiquines) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="row">
    <!-- Formulario para agregar botiquin -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="section-title bg-primary text-white">
                <i class="fas fa-first-aid me-2"></i> Crear Botiquín
            </div>
            <div class="card-body">
                <form id="formCrearBotiquin" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/BotiquinHandler.php" method="POST" class="card-form">
                    <input type="hidden" name="action" value="crear">
                    
                    <div class="mb-3">
                        <label for="hospital_botiquin_create" class="form-label">Hospital <span class="text-danger">*</span></label>
                        <select class="form-select" id="hospital_botiquin_create" required>
                            <option value="">Seleccione un hospital</option>
                            <?php foreach ($hospitales as $hospital): ?>
                                <option value="<?= $hospital->getIdHospital() ?>"><?= htmlspecialchars($hospital->getNombre()) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="planta_botiquin_create" class="form-label">Planta <span class="text-danger">*</span></label>
                        <select class="form-select" id="planta_botiquin_create" name="planta_id" required disabled>
                            <option value="">Primero seleccione un hospital</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nombre_botiquin_create" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre_botiquin_create" name="nombre" required>
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
    
    <!-- Formulario para editar botiquin -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="section-title bg-warning text-dark">
                <i class="fas fa-edit me-2"></i> Editar Botiquín
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="select_botiquin" class="form-label">Seleccionar Botiquín</label>
                    <select class="form-select" id="select_botiquin">
                        <option value="">Seleccione un botiquín para editar</option>
                        <?php foreach ($botiquines as $botiquin): 
                            $plantaBotiquin = null;
                            foreach ($plantas as $p) {
                                if ($p->getIdPlanta() == $botiquin->getIdPlanta()) {
                                    $plantaBotiquin = $p;
                                    break;
                                }
                            }

                            $hospitalBotiquin = null;
                            if ($plantaBotiquin) {
                                foreach ($hospitales as $h) {
                                    if ($h->getIdHospital() == $plantaBotiquin->getIdHospital()) {
                                        $hospitalBotiquin = $h;
                                        break;
                                    }
                                }
                            }
                            
                            $ubicacion = "";
                            if ($plantaBotiquin && $hospitalBotiquin) {
                                $ubicacion = htmlspecialchars($plantaBotiquin->getNombre()) . ' - ' . htmlspecialchars($hospitalBotiquin->getNombre());
                            }
                        ?>
                            <option value="<?= $botiquin->getIdBotiquines() ?>">
                                <?= htmlspecialchars($botiquin->getNombre()) ?> (<?= $ubicacion ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div id="editar_botiquin_form_container" style="display: none;">
                    <form id="formEditarBotiquin" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/BotiquinHandler.php" method="POST" class="card-form">
                        <input type="hidden" name="action" value="editar">
                        <input type="hidden" id="editar_botiquin_id" name="id">
                        
                        <div class="mb-3">
                            <label for="hospital_botiquin_edit" class="form-label">Hospital <span class="text-danger">*</span></label>
                            <select class="form-select" id="hospital_botiquin_edit" required>
                                <option value="">Seleccione un hospital</option>
                                <?php foreach ($hospitales as $hospital): ?>
                                    <option value="<?= $hospital->getIdHospital() ?>"><?= htmlspecialchars($hospital->getNombre()) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="planta_botiquin_edit" class="form-label">Planta <span class="text-danger">*</span></label>
                            <select class="form-select" id="planta_botiquin_edit" name="planta_id" required>
                                <option value="">Primero seleccione un hospital</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nombre_botiquin_edit" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre_botiquin_edit" name="nombre" required>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" id="cancelar_edicion_botiquin" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar
                            </button>
                        </div>
                    </form>
                </div>
                
                <div id="editar_botiquin_placeholder" class="text-center text-muted py-4">
                    <i class="fas fa-first-aid fa-3x mb-3"></i>
                    <p>Seleccione un botiquín para editarlo</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario de creación
    const hospitalBotiquinCreate = document.getElementById('hospital_botiquin_create');
    const plantaBotiquinCreate = document.getElementById('planta_botiquin_create');
    
    // Elementos del formulario de edición
    const selectBotiquin = document.getElementById('select_botiquin');
    const editarBotiquinFormContainer = document.getElementById('editar_botiquin_form_container');
    const editarBotiquinPlaceholder = document.getElementById('editar_botiquin_placeholder');
    const editarBotiquinId = document.getElementById('editar_botiquin_id');
    const hospitalBotiquinEdit = document.getElementById('hospital_botiquin_edit');
    const plantaBotiquinEdit = document.getElementById('planta_botiquin_edit');
    const nombreBotiquinEdit = document.getElementById('nombre_botiquin_edit');
    const cancelarEdicionBotiquin = document.getElementById('cancelar_edicion_botiquin');
    
    // Mapa de plantas por hospital para ambos formularios
    const plantasPorHospital = {};
    
    // Inicializar mapa de plantas por hospital
    <?php
    $tempMap = [];
    foreach ($plantas as $planta) {
        if (!isset($tempMap[$planta->getIdHospital()])) {
            $tempMap[$planta->getIdHospital()] = [];
        }
        $tempMap[$planta->getIdHospital()][] = [
            'id' => $planta->getIdPlanta(),
            'nombre' => $planta->getNombre()
        ];
    }
    
    echo "const plantasData = " . json_encode($tempMap) . ";";
    
    // Crear un mapa para buscar rápidamente los datos del botiquín por ID
    echo "const botiquinesData = {};";
    foreach ($botiquines as $botiquin) {
        echo "botiquinesData[" . $botiquin->getIdBotiquines() . "] = {";
        echo "id: " . $botiquin->getIdBotiquines() . ",";
        echo "nombre: '" . addslashes($botiquin->getNombre()) . "',";
        echo "planta_id: " . $botiquin->getIdPlanta();
        echo "};";
    }
    
    // Crear un mapa para buscar rápidamente el hospital de una planta
    echo "const hospitalPorPlanta = {};";
    foreach ($plantas as $planta) {
        echo "hospitalPorPlanta[" . $planta->getIdPlanta() . "] = " . $planta->getIdHospital() . ";";
    }
    ?>
    
    // Función para actualizar las plantas según el hospital seleccionado
    function actualizarPlantas(hospitalId, plantaSelect, selectedPlantaId = null) {
        plantaSelect.innerHTML = '<option value="">Seleccione una planta</option>';
        
        if (hospitalId) {
            plantaSelect.disabled = false;
            const plantas = plantasData[hospitalId] || [];
            
            plantas.forEach(planta => {
                const option = document.createElement('option');
                option.value = planta.id;
                option.textContent = planta.nombre;
                if (selectedPlantaId && planta.id == selectedPlantaId) {
                    option.selected = true;
                }
                plantaSelect.appendChild(option);
            });
        } else {
            plantaSelect.disabled = true;
        }
    }
    
    // Manejar cambio de hospital en el formulario de creación
    hospitalBotiquinCreate.addEventListener('change', function() {
        actualizarPlantas(this.value, plantaBotiquinCreate);
    });
    
    // Manejar cambio de hospital en el formulario de edición
    hospitalBotiquinEdit.addEventListener('change', function() {
        actualizarPlantas(this.value, plantaBotiquinEdit);
    });
    
    // Manejar la selección de un botiquín para editar
    selectBotiquin.addEventListener('change', function() {
        const botiquinId = this.value;
        
        if (botiquinId) {
            const botiquin = botiquinesData[botiquinId];
            if (botiquin) {
                // Llenar el formulario con los datos del botiquín
                editarBotiquinId.value = botiquin.id;
                nombreBotiquinEdit.value = botiquin.nombre;
                
                // Establecer la planta y el hospital correspondiente
                const plantaId = botiquin.planta_id;
                const hospitalId = hospitalPorPlanta[plantaId];
                
                // Primero seleccionar el hospital
                hospitalBotiquinEdit.value = hospitalId;
                
                // Luego cargar y seleccionar la planta
                actualizarPlantas(hospitalId, plantaBotiquinEdit, plantaId);
                
                // Mostrar el formulario y ocultar el placeholder
                editarBotiquinFormContainer.style.display = 'block';
                editarBotiquinPlaceholder.style.display = 'none';
            } else {
                console.error('Error: No se encontraron datos para el botiquín seleccionado');
            }
        } else {
            // Ocultar el formulario y mostrar el placeholder
            editarBotiquinFormContainer.style.display = 'none';
            editarBotiquinPlaceholder.style.display = 'block';
        }
    });
    
    // Manejar el botón de cancelar edición
    cancelarEdicionBotiquin.addEventListener('click', function() {
        selectBotiquin.value = '';
        editarBotiquinFormContainer.style.display = 'none';
        editarBotiquinPlaceholder.style.display = 'block';
    });
});
</script>
