<?php
if (!isset($hospitales) || !isset($plantaController) || !isset($plantas) || !isset($almacenes) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="row">
    <!-- Formulario para agregar almacén -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="section-title bg-primary text-white">
                <i class="fas fa-warehouse me-2"></i> Crear Almacén
            </div>
            <div class="card-body">
                <form id="formCrearAlmacen" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/AlmacenesController.php" method="POST" class="card-form">
                    <input type="hidden" name="action" value="crear">
                    
                    <div class="mb-3">
                        <label for="hospital_almacen_create" class="form-label">Hospital <span class="text-danger">*</span></label>
                        <select class="form-select" id="hospital_almacen_create" required>
                            <option value="">Seleccione un hospital</option>
                            <?php foreach ($hospitales as $hospital): ?>
                                <option value="<?= $hospital->getIdHospital() ?>"><?= htmlspecialchars($hospital->getNombre()) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="planta_almacen_create" class="form-label">Planta <span class="text-danger">*</span></label>
                        <select class="form-select" id="planta_almacen_create" name="planta_id" required disabled>
                            <option value="">Primero seleccione un hospital</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tipo_almacen_create" class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select class="form-select" id="tipo_almacen_create" name="tipo" required>
                            <option value="General">General</option>
                            <option value="Planta">Planta</option>
                        </select>
                    </div>

                    <input type="hidden" id="hospital_id_create" name="hospital_id" value="">
                    
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
    
    <!-- Formulario para editar almacén -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="section-title bg-warning text-dark">
                <i class="fas fa-edit me-2"></i> Editar Almacén
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="select_almacen" class="form-label">Seleccionar Almacén</label>
                    <select class="form-select" id="select_almacen">
                        <option value="">Seleccione un almacén para editar</option>
                        <?php foreach ($almacenes as $almacen): 
                            $plantaAlmacen = null;
                            foreach ($plantas as $p) {
                                if ($p->getIdPlanta() == $almacen->getIdPlanta()) {
                                    $plantaAlmacen = $p;
                                    break;
                                }
                            }

                            $hospitalAlmacen = null;
                            foreach ($hospitales as $h) {
                                if ($h->getIdHospital() == $almacen->getIdHospital()) {
                                    $hospitalAlmacen = $h;
                                    break;
                                }
                            }
                            
                            $ubicacion = "";
                            if ($plantaAlmacen && $hospitalAlmacen) {
                                $ubicacion = htmlspecialchars($plantaAlmacen->getNombre()) . ' - ' . htmlspecialchars($hospitalAlmacen->getNombre());
                            } elseif ($hospitalAlmacen) {
                                $ubicacion = htmlspecialchars($hospitalAlmacen->getNombre());
                            }
                        ?>
                            <option value="<?= $almacen->getIdAlmacen() ?>">
                                <?= htmlspecialchars($almacen->getTipo()) ?> (<?= $ubicacion ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div id="editar_almacen_form_container" style="display: none;">
                    <form id="formEditarAlmacen" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/AlmacenesController.php" method="POST" class="card-form">
                        <input type="hidden" name="action" value="editar">
                        <input type="hidden" id="editar_almacen_id" name="id">
                        
                        <div class="mb-3">
                            <label for="hospital_almacen_edit" class="form-label">Hospital <span class="text-danger">*</span></label>
                            <select class="form-select" id="hospital_almacen_edit" required>
                                <option value="">Seleccione un hospital</option>
                                <?php foreach ($hospitales as $hospital): ?>
                                    <option value="<?= $hospital->getIdHospital() ?>"><?= htmlspecialchars($hospital->getNombre()) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="planta_almacen_edit" class="form-label">Planta <span class="text-danger">*</span></label>
                            <select class="form-select" id="planta_almacen_edit" name="planta_id" required>
                                <option value="">Primero seleccione un hospital</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tipo_almacen_edit" class="form-label">Tipo <span class="text-danger">*</span></label>
                            <select class="form-select" id="tipo_almacen_edit" name="tipo" required>
                                <option value="General">General</option>
                                <option value="Planta">Planta</option>
                            </select>
                        </div>

                        <input type="hidden" id="hospital_id_edit" name="hospital_id" value="">
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" id="cancelar_edicion_almacen" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar
                            </button>
                        </div>
                    </form>
                </div>
                
                <div id="editar_almacen_placeholder" class="text-center text-muted py-4">
                    <i class="fas fa-warehouse fa-3x mb-3"></i>
                    <p>Seleccione un almacén para editarlo</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario de creación
    const hospitalAlmacenCreate = document.getElementById('hospital_almacen_create');
    const plantaAlmacenCreate = document.getElementById('planta_almacen_create');
    const hospitalIdCreate = document.getElementById('hospital_id_create');
    
    // Elementos del formulario de edición
    const selectAlmacen = document.getElementById('select_almacen');
    const editarAlmacenFormContainer = document.getElementById('editar_almacen_form_container');
    const editarAlmacenPlaceholder = document.getElementById('editar_almacen_placeholder');
    const editarAlmacenId = document.getElementById('editar_almacen_id');
    const hospitalAlmacenEdit = document.getElementById('hospital_almacen_edit');
    const plantaAlmacenEdit = document.getElementById('planta_almacen_edit');
    const tipoAlmacenEdit = document.getElementById('tipo_almacen_edit');
    const hospitalIdEdit = document.getElementById('hospital_id_edit');
    const cancelarEdicionAlmacen = document.getElementById('cancelar_edicion_almacen');
    
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
    
    // Crear un mapa para buscar rápidamente los datos del almacén por ID
    echo "const almacenesData = {};";
    foreach ($almacenes as $almacen) {
        echo "almacenesData[" . $almacen->getIdAlmacen() . "] = {";
        echo "id: " . $almacen->getIdAlmacen() . ",";
        echo "tipo: '" . addslashes($almacen->getTipo()) . "',";
        echo "planta_id: " . ($almacen->getIdPlanta() !== null ? $almacen->getIdPlanta() : 'null') . ",";
        echo "hospital_id: " . $almacen->getIdHospital();
        echo "};";
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
    hospitalAlmacenCreate.addEventListener('change', function() {
        const hospitalId = this.value;
        actualizarPlantas(hospitalId, plantaAlmacenCreate);
        hospitalIdCreate.value = hospitalId;
    });
    
    // Manejar cambio de hospital en el formulario de edición
    hospitalAlmacenEdit.addEventListener('change', function() {
        const hospitalId = this.value;
        actualizarPlantas(hospitalId, plantaAlmacenEdit);
        hospitalIdEdit.value = hospitalId;
    });
    
    // Manejar la selección de un almacén para editar
    selectAlmacen.addEventListener('change', function() {
        const almacenId = this.value;
        
        if (almacenId) {
            const almacen = almacenesData[almacenId];
            if (almacen) {
                // Llenar el formulario con los datos del almacén
                editarAlmacenId.value = almacen.id;
                tipoAlmacenEdit.value = almacen.tipo;
                
                // Establecer el hospital
                const hospitalId = almacen.hospital_id;
                hospitalAlmacenEdit.value = hospitalId;
                hospitalIdEdit.value = hospitalId;
                
                // Cargar y seleccionar la planta
                actualizarPlantas(hospitalId, plantaAlmacenEdit, almacen.planta_id);
                
                // Mostrar el formulario y ocultar el placeholder
                editarAlmacenFormContainer.style.display = 'block';
                editarAlmacenPlaceholder.style.display = 'none';
            } else {
                console.error('Error: No se encontraron datos para el almacén seleccionado');
            }
        } else {
            // Ocultar el formulario y mostrar el placeholder
            editarAlmacenFormContainer.style.display = 'none';
            editarAlmacenPlaceholder.style.display = 'block';
        }
    });
    
    // Manejar el botón de cancelar edición
    cancelarEdicionAlmacen.addEventListener('click', function() {
        selectAlmacen.value = '';
        editarAlmacenFormContainer.style.display = 'none';
        editarAlmacenPlaceholder.style.display = 'block';
    });
});
</script>
