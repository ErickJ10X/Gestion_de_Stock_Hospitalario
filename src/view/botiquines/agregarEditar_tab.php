<?php
if (!isset($hospitales) || !isset($plantaController) || !isset($plantas) || !isset($botiquines) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}
?>

<div class="botiquines-container">
    <div class="form-toggle-buttons mb-4">
        <button type="button" class="btn form-toggle-btn active" data-target="crearForm">
            <i class="fas fa-first-aid"></i> Crear Botiquín
        </button>
        <button type="button" class="btn form-toggle-btn" data-target="editarForm">
            <i class="fas fa-edit"></i> Editar Botiquín
        </button>
    </div>

    <div class="row">
        <!-- Formulario para crear botiquin -->
        <div class="col-md-12 form-section" id="crearForm">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form id="formCrearBotiquin" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/BotiquinController.php" method="POST" class="card-form">
                        <input type="hidden" name="action" value="crear">
                        
                        <div class="row">
                            <div class="form-group mb-3">
                                <label for="hospital_botiquin_create" class="form-label">Hospital <span class="text-danger">*</span></label>
                                <select class="form-select" id="hospital_botiquin_create" required>
                                    <option value="">Seleccione un hospital</option>
                                    <?php foreach ($hospitales as $hospital): ?>
                                        <option value="<?= $hospital->getIdHospital() ?>"><?= htmlspecialchars($hospital->getNombre()) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="form-group mb-3">
                                <label for="planta_botiquin_create" class="form-label">Planta <span class="text-danger">*</span></label>
                                <select class="form-select" id="planta_botiquin_create" name="planta_id" required disabled>
                                    <option value="">Primero seleccione un hospital</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="form-group mb-3">
                                <label for="nombre_botiquin_create" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre_botiquin_create" name="nombre" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Los campos marcados con <span class="text-danger">*</span> son obligatorios.
                                </small>
                            </div>
                        </div>
                        
                        <div class="form-group mt-4 text-center">
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-broom"></i> Limpiar
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Formulario para editar botiquin -->
        <div class="col-md-12 form-section" id="editarForm" style="display: none;">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <?php if (count($botiquines) > 0): ?>
                        <!-- Selector de botiquín -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="select_botiquin" class="form-label">Seleccione un botiquín</label>
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
                                            <option value="<?= $botiquin->getIdBotiquin() ?>">
                                                <?= htmlspecialchars($botiquin->getNombre()) ?> (<?= $ubicacion ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Seleccione un botiquín para editar sus datos</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mensaje de selección inicial -->
                        <div id="mensajeSeleccion" class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i> Seleccione un botiquín para editar
                        </div>
                        
                        <!-- Formulario de edición oculto inicialmente -->
                        <div id="editar_botiquin_form_container" style="display: none;">
                            <form id="formEditarBotiquin" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/BotiquinController.php" method="POST" class="card-form">
                                <input type="hidden" name="action" value="editar">
                                <input type="hidden" id="editar_botiquin_id" name="id">
                                
                                <div class="row">
                                    <div class="form-group mb-3">
                                        <label for="hospital_botiquin_edit" class="form-label">Hospital <span class="text-danger">*</span></label>
                                        <select class="form-select" id="hospital_botiquin_edit" required>
                                            <option value="">Seleccione un hospital</option>
                                            <?php foreach ($hospitales as $hospital): ?>
                                                <option value="<?= $hospital->getIdHospital() ?>"><?= htmlspecialchars($hospital->getNombre()) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group mb-3">
                                        <label for="planta_botiquin_edit" class="form-label">Planta <span class="text-danger">*</span></label>
                                        <select class="form-select" id="planta_botiquin_edit" name="planta_id" required>
                                            <option value="">Primero seleccione un hospital</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="form-group mb-3">
                                        <label for="nombre_botiquin_edit" class="form-label">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nombre_botiquin_edit" name="nombre" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i> Los campos marcados con <span class="text-danger">*</span> son obligatorios.
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="form-group mt-4 text-center">
                                    <button type="button" id="cancelar_edicion_botiquin" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Actualizar
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> No hay botiquines disponibles para editar.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Inicializar datos necesarios para el funcionamiento del JS
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
    
    echo "window.plantasData = " . json_encode($tempMap) . ";";
    
    // Crear un mapa para buscar rápidamente los datos del botiquín por ID
    echo "window.botiquinesData = {};";
    foreach ($botiquines as $botiquin) {
        echo "window.botiquinesData[" . $botiquin->getIdBotiquin() . "] = {";
        echo "id: " . $botiquin->getIdBotiquin() . ",";
        echo "nombre: '" . addslashes($botiquin->getNombre()) . "',";
        echo "planta_id: " . $botiquin->getIdPlanta();
        echo "};";
    }
    
    // Crear un mapa para buscar rápidamente el hospital de una planta
    echo "window.hospitalPorPlanta = {};";
    foreach ($plantas as $planta) {
        echo "window.hospitalPorPlanta[" . $planta->getIdPlanta() . "] = " . $planta->getIdHospital() . ";";
    }
    ?>
</script>
