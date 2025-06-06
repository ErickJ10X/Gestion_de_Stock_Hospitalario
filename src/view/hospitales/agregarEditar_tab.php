<?php
if (!isset($hospitales) || !isset($plantaController) || !isset($plantas) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="hospitales-container">
    <div class="form-toggle-buttons mb-4">
        <button type="button" class="btn form-toggle-btn active" data-target="crearHospitalForm">
            <i class="fas fa-hospital-alt"></i> Crear Hospital
        </button>
        <button type="button" class="btn form-toggle-btn" data-target="editarHospitalForm">
            <i class="fas fa-hospital"></i> Editar Hospital
        </button>
        <button type="button" class="btn form-toggle-btn" data-target="crearPlantaForm">
            <i class="fas fa-layer-group"></i> Crear Planta
        </button>
        <button type="button" class="btn form-toggle-btn" data-target="editarPlantaForm">
            <i class="fas fa-edit"></i> Editar Planta
        </button>
    </div>

    <div class="row">
        <!-- Formulario para crear hospital -->
        <div class="col-md-12 form-section" id="crearHospitalForm">
            <div class="card shadow mb-4">
                <form id="formCrearHospital" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/HospitalController.php" method="POST" class="card-form">
                    <input type="hidden" name="action" value="crear">
                    
                    <div class="row">
                        <div class="form-group mb-3">
                            <label for="nombre_hospital" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre_hospital" name="nombre" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="form-group mb-3">
                            <label for="ubicacion_hospital" class="form-label">Ubicación</label>
                            <input type="text" class="form-control" id="ubicacion_hospital" name="ubicacion">
                            <div class="form-text">Dirección o descripción de la ubicación</div>
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
        
        <!-- Formulario para editar hospital -->
        <div class="col-md-12 form-section" id="editarHospitalForm" style="display: none;">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <?php if (count($hospitales) > 0): ?>
                        <!-- Selector de hospital -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="select_hospital" class="form-label">Seleccione un hospital</label>
                                    <select class="form-select" id="select_hospital">
                                        <option value="">Seleccione un hospital para editar</option>
                                        <?php foreach ($hospitales as $hospital): ?>
                                            <option value="<?= $hospital->getIdHospital() ?>"><?= htmlspecialchars($hospital->getNombre()) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Seleccione un hospital para editar sus datos</div>
                                </div>
                            </div>
                        </div>

                        <!-- Mensaje de selección inicial -->
                        <div id="mensajeSeleccionHospital" class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i> Seleccione un hospital para editar
                        </div>
                    
                        <!-- Formulario de edición -->
                        <div id="editar_hospital_form_container" style="display: none;">
                            <form id="formEditarHospital" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/HospitalController.php" method="POST" class="card-form">
                                <input type="hidden" name="action" value="editar">
                                <input type="hidden" id="editar_hospital_id" name="id">
                                
                                <div class="row">
                                    <div class="form-group mb-3">
                                        <label for="editar_nombre_hospital" class="form-label">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editar_nombre_hospital" name="nombre" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group mb-3">
                                        <label for="editar_ubicacion_hospital" class="form-label">Ubicación</label>
                                        <input type="text" class="form-control" id="editar_ubicacion_hospital" name="ubicacion">
                                    </div>
                                </div>
                                
                                <div class="form-group mt-4 text-center">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Actualizar
                                    </button>
                                    <button type="button" id="cancelar_edicion_hospital" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> No hay hospitales disponibles para editar.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Formulario para crear planta -->
        <div class="col-md-12 form-section" id="crearPlantaForm" style="display: none;">
            <div class="card shadow mb-4">
                <form id="formCrearPlanta" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/PlantaController.php" method="POST" class="card-form">
                    <input type="hidden" name="action" value="crear">
                    
                    <div class="row">
                        <div class="form-group mb-3">
                            <label for="nombre_planta" class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre_planta" name="nombre" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="form-group mb-3">
                            <label for="hospital_planta" class="form-label">Hospital <span class="text-danger">*</span></label>
                            <select class="form-select" id="hospital_planta" name="id_hospital" required>
                                <option value="">Seleccione un hospital</option>
                                <?php foreach ($hospitales as $hospital): ?>
                                    <option value="<?= $hospital->getIdHospital() ?>"><?= htmlspecialchars($hospital->getNombre()) ?></option>
                                <?php endforeach; ?>
                            </select>
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
        
        <!-- Formulario para editar planta -->
        <div class="col-md-12 form-section" id="editarPlantaForm" style="display: none;">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <?php if (count($plantas) > 0): ?>
                        <!-- Selector de planta -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="select_planta" class="form-label">Seleccione una planta</label>
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
                                    <div class="form-text">Seleccione una planta para editar sus datos</div>
                                </div>
                            </div>
                        </div>

                        <!-- Mensaje de selección inicial -->
                        <div id="mensajeSeleccionPlanta" class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i> Seleccione una planta para editar
                        </div>
                    
                        <!-- Formulario de edición -->
                        <div id="editar_planta_form_container" style="display: none;">
                            <form id="formEditarPlanta" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/PlantaController.php" method="POST" class="card-form">
                                <input type="hidden" name="action" value="editar">
                                <input type="hidden" id="editar_planta_id" name="id">
                                
                                <div class="row">
                                    <div class="form-group mb-3">
                                        <label for="editar_nombre_planta" class="form-label">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editar_nombre_planta" name="nombre" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group mb-3">
                                        <label for="editar_hospital_planta" class="form-label">Hospital <span class="text-danger">*</span></label>
                                        <select class="form-select" id="editar_hospital_planta" name="id_hospital" required>
                                            <option value="">Seleccione un hospital</option>
                                            <?php foreach ($hospitales as $hospital): ?>
                                                <option value="<?= $hospital->getIdHospital() ?>"><?= htmlspecialchars($hospital->getNombre()) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group mt-4 text-center">
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-save"></i> Actualizar
                                    </button>
                                    <button type="button" id="cancelar_edicion_planta" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> No hay plantas disponibles para editar.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inicializacion de datos para los datos de hospitales y plantas -->
<script>
// Preparar datos de los hospitales para JavaScript
window.hospitalesDataObj = {};
<?php foreach ($hospitales as $hospital): ?>
window.hospitalesDataObj[<?= $hospital->getIdHospital() ?>] = {
    id: <?= $hospital->getIdHospital() ?>,
    nombre: <?= json_encode($hospital->getNombre()) ?>,
    ubicacion: <?= json_encode($hospital->getUbicacion()) ?>
};
<?php endforeach; ?>

// Preparar datos de las plantas para JavaScript
window.plantasDataObj = {};
<?php foreach ($plantas as $planta): ?>
window.plantasDataObj[<?= $planta->getIdPlanta() ?>] = {
    id: <?= $planta->getIdPlanta() ?>,
    nombre: <?= json_encode($planta->getNombre()) ?>,
    idHospital: <?= $planta->getIdHospital() ?>
};
<?php endforeach; ?>
</script>
