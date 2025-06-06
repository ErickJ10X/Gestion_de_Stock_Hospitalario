<?php
if (!isset($hospitales) || !isset($plantaController) || !isset($plantas) || !isset($almacenes) || !isset($session)) {
    die("Error: No se han proporcionado las variables requeridas.");
}

// Determinar si estamos en modo edición
$modoEdicion = isset($_GET['edit']) && $_GET['edit'] > 0;
?>

<div class="almacenes-container">
    <div class="form-toggle-buttons mb-4">
        <button type="button" class="btn form-toggle-btn <?= !$modoEdicion ? 'active' : '' ?>" data-target="crearForm">
            <i class="fas fa-warehouse"></i> Crear Almacén
        </button>
        <button type="button" class="btn form-toggle-btn <?= $modoEdicion ? 'active' : '' ?>" data-target="editarForm">
            <i class="fas fa-edit"></i> Editar Almacén
        </button>
    </div>

    <div class="row">
        <!-- Formulario para crear almacén -->
        <div class="col-md-12 form-section" id="crearForm" <?= $modoEdicion ? 'style="display: none;"' : '' ?>>
            <div class="card shadow mb-4">
                <form id="formCrearAlmacen" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/AlmacenesController.php" method="POST" class="card-form">
                    <input type="hidden" name="action" value="crear">

                    <div class="row">
                        <div class="form-group mb-3">
                            <label for="hospital_almacen_create" class="form-label">Hospital <span class="text-danger">*</span></label>
                            <select class="form-select" id="hospital_almacen_create" required>
                                <option value="">Seleccione un hospital</option>
                                <?php foreach ($hospitales as $hospital): ?>
                                    <option value="<?= $hospital->getIdHospital() ?>"><?= htmlspecialchars($hospital->getNombre()) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group mb-3">
                            <label for="planta_almacen_create" class="form-label">Planta <span class="text-danger">*</span></label>
                            <select class="form-select" id="planta_almacen_create" name="planta_id" disabled>
                                <option value="">Primero seleccione un hospital</option>
                            </select>
                            <small class="form-text text-muted">Solo requerida para almacenes de tipo Planta</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tipo_almacen_create" class="form-label">Tipo <span class="text-danger">*</span></label>
                            <select class="form-select" id="tipo_almacen_create" name="tipo" required>
                                <option value="General">General</option>
                                <option value="Planta">Planta</option>
                            </select>
                        </div>
                    </div>

                    <input type="hidden" id="hospital_id_create" name="hospital_id" value="">

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

        <!-- Formulario para editar almacén -->
        <div class="col-md-12 form-section" id="editarForm" <?= !$modoEdicion ? 'style="display: none;"' : '' ?>>
            <div class="card shadow mb-4">
                <div class="card-body">
                    <?php if (count($almacenes) > 0): ?>
                        <!-- Selector de almacén -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="select_almacen" class="form-label">Seleccione un almacén</label>
                                    <select class="form-select" id="select_almacen">
                                        <option value="">Seleccionar almacén</option>
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
                                    <div class="form-text">Seleccione un almacén para editar sus datos</div>
                                </div>
                            </div>
                        </div>

                        <!-- Mensaje de selección inicial -->
                        <div id="editar_almacen_placeholder" class="almacen-placeholder">
                            <i class="fas fa-warehouse almacen-icon"></i>
                            <p>Seleccione un almacén para editarlo</p>
                        </div>

                        <!-- Formulario de edición -->
                        <div id="editar_almacen_form_container" style="display: none;">
                            <form id="formEditarAlmacen" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/AlmacenesController.php" method="POST" class="card-form">
                                <input type="hidden" name="action" value="editar">
                                <input type="hidden" id="editar_almacen_id" name="id">

                                <div class="row">
                                    <div class="form-group mb-3">
                                        <label for="hospital_almacen_edit" class="form-label">Hospital <span class="text-danger">*</span></label>
                                        <select class="form-select" id="hospital_almacen_edit" name="hospital_id" required>
                                            <option value="">Seleccione un hospital</option>
                                            <?php foreach ($hospitales as $hospital): ?>
                                                <option value="<?= $hospital->getIdHospital() ?>"><?= htmlspecialchars($hospital->getNombre()) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group mb-3">
                                        <label for="planta_almacen_edit" class="form-label">Planta <span class="text-danger">*</span></label>
                                        <select class="form-select" id="planta_almacen_edit" name="planta_id">
                                            <option value="">Primero seleccione un hospital</option>
                                        </select>
                                        <small class="form-text text-muted">Solo requerida para almacenes de tipo Planta</small>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="tipo_almacen_edit" class="form-label">Tipo <span class="text-danger">*</span></label>
                                        <select class="form-select" id="tipo_almacen_edit" name="tipo" required>
                                            <option value="General">General</option>
                                            <option value="Planta">Planta</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group mt-4 text-center">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-save"></i> Actualizar
                                    </button>
                                    <button type="button" id="cancelar_edicion_almacen" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> No hay almacenes disponibles para editar.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inicialización de datos de almacenes para JS -->
<script>
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
</script>