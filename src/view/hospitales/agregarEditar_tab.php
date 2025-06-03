<?php
if (!isset($hospitales) || !isset($plantaController) || !isset($plantas) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="row">
    <!-- Columna para gestión de hospitales -->
    <div class="col-lg-6">
        <!-- Formulario para agregar hospital -->
        <div class="card shadow mb-4">
            <div class="section-title bg-primary text-white">
                <i class="fas fa-hospital-alt me-2"></i> Crear Hospital
            </div>
            <div class="card-body">
                <form id="formCrearHospital" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/HospitalHandler.php" method="POST" class="card-form">
                    <input type="hidden" name="action" value="crear">
                    
                    <div class="mb-3">
                        <label for="nombre_hospital" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre_hospital" name="nombre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ubicacion_hospital" class="form-label">Ubicación</label>
                        <input type="text" class="form-control" id="ubicacion_hospital" name="ubicacion">
                        <div class="form-text">Dirección o descripción de la ubicación</div>
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
        
        <!-- Formulario para editar hospital -->
        <div class="card shadow mb-4">
            <div class="section-title bg-warning text-dark">
                <i class="fas fa-edit me-2"></i> Editar Hospital
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="select_hospital" class="form-label">Seleccionar Hospital</label>
                    <select class="form-select" id="select_hospital">
                        <option value="">Seleccione un hospital para editar</option>
                        <?php foreach ($hospitales as $hospital): ?>
                            <option value="<?= $hospital->getIdHospital() ?>"><?= htmlspecialchars($hospital->getNombre()) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div id="editar_hospital_form_container" style="display: none;">
                    <form id="formEditarHospital" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/HospitalHandler.php" method="POST" class="card-form">
                        <input type="hidden" name="action" value="editar">
                        <input type="hidden" id="editar_hospital_id" name="id">
                        
                        <div class="mb-3">
                            <label for="editar_nombre_hospital" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editar_nombre_hospital" name="nombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editar_ubicacion_hospital" class="form-label">Ubicación</label>
                            <input type="text" class="form-control" id="editar_ubicacion_hospital" name="ubicacion">
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" id="cancelar_edicion_hospital" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Actualizar
                            </button>
                        </div>
                    </form>
                </div>
                
                <div id="editar_hospital_placeholder" class="text-center text-muted py-4">
                    <i class="fas fa-hospital fa-3x mb-3"></i>
                    <p>Seleccione un hospital para editarlo</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Columna para gestión de plantas -->
    <div class="col-lg-6">
        <!-- Formulario para agregar planta -->
        <div class="card shadow mb-4">
            <div class="section-title bg-success text-white">
                <i class="fas fa-layer-group me-2"></i> Crear Planta
            </div>
            <div class="card-body">
                <form id="formCrearPlanta" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/PlantaHandler.php" method="POST" class="card-form">
                    <input type="hidden" name="action" value="crear">
                    
                    <div class="mb-3">
                        <label for="nombre_planta" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre_planta" name="nombre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="hospital_planta" class="form-label">Hospital <span class="text-danger">*</span></label>
                        <select class="form-select" id="hospital_planta" name="id_hospital" required>
                            <option value="">Seleccione un hospital</option>
                            <?php foreach ($hospitales as $hospital): ?>
                                <option value="<?= $hospital->getIdHospital() ?>"><?= htmlspecialchars($hospital->getNombre()) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="reset" class="btn btn-secondary me-2">
                            <i class="fas fa-broom"></i> Limpiar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Formulario para editar planta -->
        <div class="card shadow mb-4">
            <div class="section-title bg-info text-white">
                <i class="fas fa-edit me-2"></i> Editar Planta
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="select_planta" class="form-label">Seleccionar Planta</label>
                    <select class="form-select" id="select_planta">
                        <option value="">Seleccione una planta para editar</option>
                        <?php foreach ($plantas as $planta): ?>
                            <?php
                            $hospitalNombre = "Desconocido";
                            foreach ($hospitales as $h) {
                                if ($h->getIdHospital() == $planta->getIdHospital()) {
                                    $hospitalNombre = $h->getNombre();
                                    break;
                                }
                            }
                            ?>
                            <option value="<?= $planta->getIdPlanta() ?>"><?= htmlspecialchars($planta->getNombre()) ?> (<?= htmlspecialchars($hospitalNombre) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div id="editar_planta_form_container" style="display: none;">
                    <form id="formEditarPlanta" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/PlantaHandler.php" method="POST" class="card-form">
                        <input type="hidden" name="action" value="editar">
                        <input type="hidden" id="editar_planta_id" name="id">
                        
                        <div class="mb-3">
                            <label for="editar_nombre_planta" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editar_nombre_planta" name="nombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editar_hospital_planta" class="form-label">Hospital <span class="text-danger">*</span></label>
                            <select class="form-select" id="editar_hospital_planta" name="id_hospital" required>
                                <option value="">Seleccione un hospital</option>
                                <?php foreach ($hospitales as $hospital): ?>
                                    <option value="<?= $hospital->getIdHospital() ?>"><?= htmlspecialchars($hospital->getNombre()) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="button" id="cancelar_edicion_planta" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-save"></i> Actualizar
                            </button>
                        </div>
                    </form>
                </div>
                
                <div id="editar_planta_placeholder" class="text-center text-muted py-4">
                    <i class="fas fa-layer-group fa-3x mb-3"></i>
                    <p>Seleccione una planta para editarla</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejo de selección de hospital para editar
    const selectHospital = document.getElementById('select_hospital');
    const editarHospitalFormContainer = document.getElementById('editar_hospital_form_container');
    const editarHospitalPlaceholder = document.getElementById('editar_hospital_placeholder');
    const editarHospitalId = document.getElementById('editar_hospital_id');
    const editarNombreHospital = document.getElementById('editar_nombre_hospital');
    const editarUbicacionHospital = document.getElementById('editar_ubicacion_hospital');
    const cancelarEdicionHospital = document.getElementById('cancelar_edicion_hospital');
    
    // Manejo de selección de planta para editar
    const selectPlanta = document.getElementById('select_planta');
    const editarPlantaFormContainer = document.getElementById('editar_planta_form_container');
    const editarPlantaPlaceholder = document.getElementById('editar_planta_placeholder');
    const editarPlantaId = document.getElementById('editar_planta_id');
    const editarNombrePlanta = document.getElementById('editar_nombre_planta');
    const editarHospitalPlanta = document.getElementById('editar_hospital_planta');
    const cancelarEdicionPlanta = document.getElementById('cancelar_edicion_planta');
    
    // Manejar la selección de un hospital para editar
    selectHospital.addEventListener('change', function() {
        const hospitalId = this.value;
        
        if (hospitalId) {
            // Cargar datos del hospital seleccionado mediante AJAX
            fetch(`/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/HospitalApi.php?action=getById&id=${hospitalId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error === false && data.hospital) {
                        // Llenar el formulario con los datos del hospital
                        editarHospitalId.value = data.hospital.id_hospital;
                        editarNombreHospital.value = data.hospital.nombre;
                        editarUbicacionHospital.value = data.hospital.ubicacion || '';
                        
                        // Mostrar el formulario y ocultar el placeholder
                        editarHospitalFormContainer.style.display = 'block';
                        editarHospitalPlaceholder.style.display = 'none';
                    } else {
                        console.error('Error al cargar datos del hospital:', data.mensaje || 'Error desconocido');
                        alert('Error al cargar datos del hospital');
                    }
                })
                .catch(error => {
                    console.error('Error en la solicitud AJAX:', error);
                    alert('Error en la solicitud');
                });
        } else {
            // Ocultar el formulario y mostrar el placeholder
            editarHospitalFormContainer.style.display = 'none';
            editarHospitalPlaceholder.style.display = 'block';
        }
    });
    
    // Manejar la selección de una planta para editar
    selectPlanta.addEventListener('change', function() {
        const plantaId = this.value;
        
        if (plantaId) {
            // Cargar datos de la planta seleccionada mediante AJAX
            fetch(`/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/PlantaApi.php?action=getById&id=${plantaId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error === false && data.planta) {
                        // Llenar el formulario con los datos de la planta
                        editarPlantaId.value = data.planta.id_planta;
                        editarNombrePlanta.value = data.planta.nombre;
                        editarHospitalPlanta.value = data.planta.id_hospital;
                        
                        // Mostrar el formulario y ocultar el placeholder
                        editarPlantaFormContainer.style.display = 'block';
                        editarPlantaPlaceholder.style.display = 'none';
                    } else {
                        console.error('Error al cargar datos de la planta:', data.mensaje || 'Error desconocido');
                        alert('Error al cargar datos de la planta');
                    }
                })
                .catch(error => {
                    console.error('Error en la solicitud AJAX:', error);
                    alert('Error en la solicitud');
                });
        } else {
            // Ocultar el formulario y mostrar el placeholder
            editarPlantaFormContainer.style.display = 'none';
            editarPlantaPlaceholder.style.display = 'block';
        }
    });
    
    // Manejar el botón de cancelar edición de hospital
    cancelarEdicionHospital.addEventListener('click', function() {
        selectHospital.value = '';
        editarHospitalFormContainer.style.display = 'none';
        editarHospitalPlaceholder.style.display = 'block';
    });
    
    // Manejar el botón de cancelar edición de planta
    cancelarEdicionPlanta.addEventListener('click', function() {
        selectPlanta.value = '';
        editarPlantaFormContainer.style.display = 'none';
        editarPlantaPlaceholder.style.display = 'block';
    });
});
</script>
