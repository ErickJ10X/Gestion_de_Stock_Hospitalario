<?php
// Verificar que tenemos los datos necesarios
if (!isset($usuarios)) {
    die("Error: No se han proporcionado las variables requeridas.");
}

// Importar controladores necesarios
require_once(__DIR__ . '/../../controller/AlmacenesController.php');
require_once(__DIR__ . '/../../controller/PlantaController.php');
require_once(__DIR__ . '/../../controller/BotiquinController.php');

use controller\AlmacenesController;
use controller\PlantaController;
use controller\BotiquinController;

// Instanciar controladores
$almacenController = new AlmacenesController();
$plantaController = new PlantaController();
$botiquinController = new BotiquinController();

// Obtener los almacenes, plantas y botiquines
try {
    $almacenes = $almacenController->index()['almacenes'] ?? [];
    $plantas = $plantaController->index()['plantas'] ?? [];
    $botiquines = $botiquinController->index()['botiquines'] ?? [];
} catch (Exception $e) {
    $almacenes = [];
    $plantas = [];
    $botiquines = [];
    echo "<div class='alert alert-danger'>Error al cargar ubicaciones: " . $e->getMessage() . "</div>";
}
?>

<div class="usuarios-container">
    <div class="form-toggle-buttons mb-4">
        <button type="button" class="btn form-toggle-btn active" data-target="asignarForm">
            <i class="fas fa-map-marker-plus"></i> Asignar Ubicación
        </button>
        <button type="button" class="btn form-toggle-btn" data-target="verUbicacionesForm">
            <i class="fas fa-map-marked-alt"></i> Ver Ubicaciones
        </button>
    </div>

    <div class="row">
        <!-- Formulario para asignar ubicación -->
        <div class="col-md-12 form-section" id="asignarForm">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form id="formAsignarUbicacion" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/UsuarioController.php"
                          method="POST" class="card-form">
                        <input type="hidden" name="action" value="asignarUbicacion">

                        <div class="row">

                                <div class="form-group mb-3">
                                    <label for="usuario_id" class="form-label">Usuario <span class="text-danger">*</span></label>
                                    <select id="usuario_id" name="usuario_id" class="form-select" required>
                                        <option value="">Seleccionar usuario</option>
                                        <?php foreach ($usuarios as $usuario): ?>
                                            <option value="<?= $usuario->getIdUsuario() ?>">
                                                <?= htmlspecialchars($usuario->getNombre()) ?> (<?= htmlspecialchars($usuario->getEmail()) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                        </div>

                        <div class="row">

                                <div class="form-group mb-3">
                                    <label for="tipo_ubicacion" class="form-label">Tipo de ubicación <span class="text-danger">*</span></label>
                                    <select id="tipo_ubicacion" name="tipo_ubicacion" class="form-select" required>
                                        <option value="">Seleccionar tipo</option>
                                        <option value="Hospital">Hospital/Almacén</option>
                                        <option value="Planta">Planta</option>
                                        <option value="Botiquin">Botiquín</option>
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="ubicacion_id" class="form-label">Ubicación <span class="text-danger">*</span></label>
                                    <select id="ubicacion_id" name="ubicacion_id" class="form-select" required disabled>
                                        <option value="">Seleccione un tipo primero</option>
                                    </select>
                                    <div class="form-text">Se habilitará cuando seleccione un tipo de ubicación</div>
                                </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> El usuario tendrá acceso a la ubicación seleccionada según su rol.
                                </small>
                            </div>
                        </div>

                        <div class="form-group mt-4 text-center">
                            <button type="submit" class="btn btn-success" id="btnAsignar">
                                <i class="fas fa-save"></i> Guardar
                            </button>
                            <button type="reset" class="btn btn-secondary" id="btnLimpiarUbicacion">
                                <i class="fas fa-broom"></i> Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sección para ver ubicaciones asignadas -->
        <div class="col-md-12 form-section" id="verUbicacionesForm" style="display: none;">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">

                                <label for="verUsuarioUbicaciones" class="form-label">Seleccione un usuario</label>
                                <select id="verUsuarioUbicaciones" class="form-select" onchange="actualizarListadoUbicaciones(this.value)">
                                    <option value="">Seleccionar usuario</option>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <option value="<?= $usuario->getIdUsuario() ?>">
                                            <?= htmlspecialchars($usuario->getNombre()) ?> (<?= htmlspecialchars($usuario->getEmail()) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Seleccione un usuario para ver sus ubicaciones asignadas</div>

                        </div>
                    </div>

                    <div id="sinUbicacionesMsg" class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i> Seleccione un usuario para ver sus ubicaciones asignadas
                    </div>

                    <form id="formCargarUbicaciones" action="" method="POST" style="display:none;">
                        <input type="hidden" name="action" value="cargarUbicacionesUsuario">
                        <input type="hidden" name="id_usuario" id="id_usuario_cargar">
                    </form>

                    <div id="tablaUbicacionesContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="tablaUbicaciones">
                                <thead class="table-light">
                                <tr>
                                    <th>Tipo</th>
                                    <th>Nombre</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (isset($ubicaciones_usuario) && is_array($ubicaciones_usuario) && !empty($ubicaciones_usuario)): ?>
                                    <?php foreach ($ubicaciones_usuario as $ubicacion): ?>
                                        <tr>
                                            <td>
                                                <?php
                                                $badgeClass = 'bg-secondary';
                                                $tipoTexto = $ubicacion['tipo_ubicacion'];
                                                switch ($ubicacion['tipo_ubicacion']) {
                                                    case 'Hospital':
                                                        $badgeClass = 'bg-primary';
                                                        $tipoTexto = 'HOSPITAL/ALMACÉN';
                                                        break;
                                                    case 'Planta':
                                                        $badgeClass = 'bg-success';
                                                        $tipoTexto = 'PLANTA';
                                                        break;
                                                    case 'Botiquin':
                                                        $badgeClass = 'bg-info';
                                                        $tipoTexto = 'BOTIQUÍN';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $badgeClass ?>"><?= $tipoTexto ?></span>
                                            </td>
                                            <td><?= $ubicacion['nombre'] ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="confirmarEliminarUbicacion(<?= $ubicacion['id'] ?? '0' ?>, '<?= htmlspecialchars($ubicacion['nombre'] ?? '') ?>')">
                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Mensaje cuando no hay ubicaciones -->
                        <?php if (isset($ubicaciones_usuario) && is_array($ubicaciones_usuario) && empty($ubicaciones_usuario)): ?>
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle me-2"></i> Este usuario no tiene ubicaciones asignadas.
                            </div>
                        <?php endif; ?>
                        
                        <!-- Botones de acción para la tabla -->
                        <div class="form-group mt-3 text-center">
                            <a href="?tab=asignar-ubicaciones" class="btn btn-secondary">
                                <i class="fas fa-sync-alt"></i> Refrescar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Datos de ubicaciones para JS
    window.datosUbicaciones = {
        almacenes: <?= json_encode(array_map(function($a) {
            return [
                'id' => $a->getIdAlmacen(),
                'nombre' => $a->getTipo() . ' - ' . ($a->getIdPlanta() ? 'Planta ' . $a->getIdPlanta() : 'General')
            ];
        }, $almacenes)) ?>,
        plantas: <?= json_encode(array_map(function($p) {
            return [
                'id' => $p->getIdPlanta(),
                'nombre' => $p->getNombre() . ' (Hospital ' . $p->getIdHospital() . ')'
            ];
        }, $plantas)) ?>,
        botiquines: <?= json_encode(array_map(function($b) {
            return [
                'id' => $b->getIdBotiquin(),
                'nombre' => $b->getNombre() . ' (Planta ' . $b->getIdPlanta() . ')'
            ];
        }, $botiquines)) ?>
    };

    function confirmarEliminarUbicacion(id, nombre) {
        if(confirm(`¿Está seguro que desea eliminar la ubicación "${nombre}" del usuario?`)) {
            // Aquí iría el código para eliminar la ubicación
            console.log("Eliminando ubicación: " + id);
            
            // Ejemplo de cómo enviar una solicitud para eliminar
            const formDelete = document.createElement('form');
            formDelete.method = 'POST';
            formDelete.action = '/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/UsuarioController.php';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'eliminarUbicacion';
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'ubicacion_id';
            idInput.value = id;
            
            formDelete.appendChild(actionInput);
            formDelete.appendChild(idInput);
            document.body.appendChild(formDelete);
            formDelete.submit();
        }
    }

    // Función para actualizar el listado de ubicaciones
    function actualizarListadoUbicaciones(userId) {
        if (!userId) {
            document.getElementById('sinUbicacionesMsg').style.display = 'block';
            document.getElementById('tablaUbicacionesContainer').style.display = 'none';
            return;
        }
        
        document.getElementById('id_usuario_cargar').value = userId;
        document.getElementById('sinUbicacionesMsg').style.display = 'none';
        document.getElementById('tablaUbicacionesContainer').style.display = 'block';
        
        // Aquí se cargarían las ubicaciones con AJAX o se enviaría el formulario
        // Por ahora solo mostramos la sección
    }

    // Si hay un usuario seleccionado por GET, seleccionarlo en el dropdown
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_GET['usuario']) && is_numeric($_GET['usuario'])): ?>
        const usuarioSelect = document.getElementById('verUsuarioUbicaciones');
        if (usuarioSelect) {
            usuarioSelect.value = '<?= $_GET['usuario'] ?>';
            setTimeout(() => {
                actualizarListadoUbicaciones(usuarioSelect.value);
            }, 100);
        }
        <?php endif; ?>

        // Mostrar tabla de ubicaciones si hay datos
        <?php if (isset($ubicaciones_usuario) && is_array($ubicaciones_usuario) && !empty($ubicaciones_usuario)): ?>
        document.getElementById('sinUbicacionesMsg').style.display = 'none';
        document.getElementById('tablaUbicacionesContainer').style.display = 'block';
        <?php endif; ?>

        // Cambio de tipo de ubicación
        const tipoUbicacionSelect = document.getElementById('tipo_ubicacion');
        const ubicacionSelect = document.getElementById('ubicacion_id');
        
        if (tipoUbicacionSelect && ubicacionSelect) {
            tipoUbicacionSelect.addEventListener('change', function() {
                const tipo = this.value;
                ubicacionSelect.innerHTML = '<option value="">Seleccione una ubicación</option>';
                
                if (!tipo) {
                    ubicacionSelect.disabled = true;
                    return;
                }
                
                ubicacionSelect.disabled = false;
                
                let datos = [];
                if (tipo === 'Hospital') datos = window.datosUbicaciones.almacenes;
                if (tipo === 'Planta') datos = window.datosUbicaciones.plantas;
                if (tipo === 'Botiquin') datos = window.datosUbicaciones.botiquines;
                
                datos.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.nombre;
                    ubicacionSelect.appendChild(option);
                });
            });
        }

        // Limpiar botón
        const btnLimpiarUbicacion = document.getElementById('btnLimpiarUbicacion');
        if (btnLimpiarUbicacion) {
            btnLimpiarUbicacion.addEventListener('click', function() {
                if (ubicacionSelect) {
                    ubicacionSelect.innerHTML = '<option value="">Seleccione un tipo primero</option>';
                    ubicacionSelect.disabled = true;
                }

                if (tipoUbicacionSelect) {
                    tipoUbicacionSelect.value = '';
                }
            });
        }
        
        // Botones para alternar entre formularios
        document.querySelectorAll('.form-toggle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                
                // Desactivar todos los botones y ocultar todos los formularios
                document.querySelectorAll('.form-toggle-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.form-section').forEach(f => f.style.display = 'none');
                
                // Activar el botón y mostrar el formulario correspondiente
                this.classList.add('active');
                document.getElementById(targetId).style.display = 'block';
            });
        });
    });
</script>
<!-- Incluir el JS centralizado de usuarios -->
<script src="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/assets/js/usuarios.js"></script>
