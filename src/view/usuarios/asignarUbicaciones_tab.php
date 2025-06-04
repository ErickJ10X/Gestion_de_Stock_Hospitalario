<?php
// Verificar que tenemos los datos necesarios
if (!isset($usuarios)) {
    die("Error: No se han proporcionado las variables requeridas.");
}

// Importar controladores y repositorios necesarios
require_once(__DIR__ . '/../../controller/AlmacenesController.php');
require_once(__DIR__ . '/../../controller/PlantaController.php');
require_once(__DIR__ . '/../../controller/BotiquinController.php');
require_once(__DIR__ . '/../../model/entity/UsuarioUbicacion.php');

use controller\AlmacenesController;
use controller\PlantaController;
use controller\BotiquinController;
use model\entity\UsuarioUbicacion;

// Instanciar controladores
$almacenController = new AlmacenesController();
$plantaController = new PlantaController();
$botiquinController = new BotiquinController();

// Obtener los almacenes, plantas y botiquines
try {
    $almacenes = $almacenController->index()['data'] ?? [];
    $plantas = $plantaController->index()['plantas'] ?? [];
    $botiquines = $botiquinController->index()['data'] ?? [];
} catch (Exception $e) {
    $almacenes = [];
    $plantas = [];
    $botiquines = [];
    echo "<div class='alert alert-danger'>Error al cargar ubicaciones: " . $e->getMessage() . "</div>";
}
?>

<div class="ubicaciones-container">
    <div class="card shadow mb-4">
        <div class="section-title bg-primary text-white">
            Asignación de ubicaciones
        </div>
        <div class="card-body">
            <form id="formAsignarUbicacion" class="row g-3">
                <div class="col-md-6">
                    <label for="usuario_id" class="form-label">Usuario <span class="text-danger">*</span></label>
                    <select id="usuario_id" name="usuario_id" class="form-select" required>
                        <option value="">Seleccionar usuario</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?= $usuario->getIdUsuario() ?>">
                                <?= htmlspecialchars($usuario->getNombre()) ?> 
                                (<?= htmlspecialchars($usuario->getEmail()) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Seleccione el usuario al que desea asignar ubicaciones</div>
                </div>

                <div class="col-md-6">
                    <label for="ubicacion_tipo" class="form-label">Tipo de ubicación <span class="text-danger">*</span></label>
                    <select id="ubicacion_tipo" name="ubicacion_tipo" class="form-select" required>
                        <option value="">Seleccionar tipo</option>
                        <option value="<?= UsuarioUbicacion::TIPO_HOSPITAL ?>">Hospital/Almacén</option>
                        <option value="<?= UsuarioUbicacion::TIPO_PLANTA ?>">Planta</option>
                        <option value="<?= UsuarioUbicacion::TIPO_BOTIQUIN ?>">Botiquín</option>
                    </select>
                    <div class="form-text">Seleccione el tipo de ubicación que desea asignar</div>
                </div>

                <div class="col-md-12">
                    <label for="ubicacion_id" class="form-label">Ubicación <span class="text-danger">*</span></label>
                    <select id="ubicacion_id" name="ubicacion_id" class="form-select" required disabled>
                        <option value="">Primero seleccione un tipo de ubicación</option>
                    </select>
                    <div class="form-text">Seleccione la ubicación específica</div>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary" id="btnAsignar">
                        <i class="fas fa-plus-circle"></i> Asignar ubicación
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-broom"></i> Limpiar
                    </button>
                </div>
            </form>

            <hr class="my-4">

            <!-- Ubicaciones asignadas -->
            <div class="row mt-4">
                <div class="col-12">
                    <h5><i class="fas fa-map-marker-alt"></i> Ubicaciones asignadas</h5>
                    <div class="alert alert-info" id="sinUbicacionesMsg">
                        <i class="fas fa-info-circle"></i> Seleccione un usuario para ver sus ubicaciones asignadas
                    </div>
                    <div class="table-responsive" id="tablaUbicacionesContainer" style="display: none;">
                        <table class="table table-hover table-bordered" id="tablaUbicaciones">
                            <thead class="table-light">
                                <tr>
                                    <th>Tipo</th>
                                    <th>Nombre</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Las ubicaciones se cargarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar ubicación -->
<div class="modal fade" id="eliminarUbicacionModal" tabindex="-1" aria-labelledby="eliminarUbicacionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="eliminarUbicacionModalLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar esta ubicación del usuario?</p>
                <p id="eliminarUbicacionNombre" class="fw-bold"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formAsignarUbicacion = document.getElementById('formAsignarUbicacion');
    const usuarioSelect = document.getElementById('usuario_id');
    const tipoUbicacionSelect = document.getElementById('ubicacion_tipo');
    const ubicacionSelect = document.getElementById('ubicacion_id');
    const sinUbicacionesMsg = document.getElementById('sinUbicacionesMsg');
    const tablaUbicacionesContainer = document.getElementById('tablaUbicacionesContainer');
    const tablaUbicaciones = document.getElementById('tablaUbicaciones');
    
    // Modal de confirmación
    const eliminarUbicacionModal = new bootstrap.Modal(document.getElementById('eliminarUbicacionModal'));
    const eliminarUbicacionNombre = document.getElementById('eliminarUbicacionNombre');
    const btnConfirmarEliminar = document.getElementById('btnConfirmarEliminar');
    
    // Almacenes, plantas y botiquines disponibles
    const almacenes = <?= json_encode(array_map(function($a) { return ['id' => $a->getId(), 'nombre' => $a->getTipo()]; }, $almacenes)) ?>;
    const plantas = <?= json_encode(array_map(function($p) { return ['id' => $p->getIdPlanta(), 'nombre' => $p->getNombre()]; }, $plantas)) ?>;
    const botiquines = <?= json_encode(array_map(function($b) { return ['id' => $b->getId(), 'nombre' => $b->getNombre()]; }, $botiquines)) ?>;
    
    // Constantes de tipos de ubicación
    const TIPO_HOSPITAL = '<?= UsuarioUbicacion::TIPO_HOSPITAL ?>';
    const TIPO_PLANTA = '<?= UsuarioUbicacion::TIPO_PLANTA ?>';
    const TIPO_BOTIQUIN = '<?= UsuarioUbicacion::TIPO_BOTIQUIN ?>';
    
    // Datos temporales para eliminar ubicación
    let tempDeleteData = null;
    
    // Manejar cambio en el tipo de ubicación
    tipoUbicacionSelect.addEventListener('change', function() {
        const tipo = this.value;
        ubicacionSelect.innerHTML = '<option value="">Seleccione una ubicación</option>';
        
        if (tipo) {
            ubicacionSelect.disabled = false;
            let ubicaciones = [];
            
            // Determinar qué lista de ubicaciones usar según el tipo seleccionado
            switch (tipo) {
                case TIPO_HOSPITAL:
                    ubicaciones = almacenes;
                    break;
                case TIPO_PLANTA:
                    ubicaciones = plantas;
                    break;
                case TIPO_BOTIQUIN:
                    ubicaciones = botiquines;
                    break;
            }
            
            // Añadir opciones al select de ubicaciones
            ubicaciones.forEach(ubicacion => {
                const option = document.createElement('option');
                option.value = ubicacion.id;
                option.textContent = ubicacion.nombre;
                ubicacionSelect.appendChild(option);
            });
        } else {
            ubicacionSelect.disabled = true;
        }
    });
    
    // Manejar cambio en el select de usuario
    usuarioSelect.addEventListener('change', function() {
        const usuarioId = this.value;
        limpiarTablaUbicaciones();
        
        if (usuarioId) {
            cargarUbicacionesUsuario(usuarioId);
        } else {
            sinUbicacionesMsg.textContent = "Seleccione un usuario para ver sus ubicaciones asignadas";
            sinUbicacionesMsg.style.display = "block";
            tablaUbicacionesContainer.style.display = "none";
        }
    });
    
    // Manejar envío del formulario
    formAsignarUbicacion.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const usuarioId = usuarioSelect.value;
        const tipoUbicacion = tipoUbicacionSelect.value;
        const ubicacionId = ubicacionSelect.value;
        
        if (!usuarioId || !tipoUbicacion || !ubicacionId) {
            mostrarMensaje('error', 'Todos los campos son obligatorios');
            return;
        }
        
        // Llamar a la API para asignar ubicación (usando el controlador existente)
        const formData = new FormData();
        formData.append('action', 'asignar');
        formData.append('usuario_id', usuarioId);
        formData.append('tipo_ubicacion', tipoUbicacion);
        formData.append('ubicacion_id', ubicacionId);
        
        fetch('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/UsuarioController.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(resultado => {
            if (resultado && resultado.success) {
                mostrarMensaje('success', 'Ubicación asignada correctamente');
                cargarUbicacionesUsuario(usuarioId);
                formAsignarUbicacion.reset();
                ubicacionSelect.disabled = true;
            } else {
                mostrarMensaje('error', 'Error: ' + (resultado ? resultado.message : 'No se pudo asignar la ubicación'));
            }
        })
        .catch(error => {
            mostrarMensaje('error', 'Error en la solicitud: ' + error);
        });
    });
    
    // Cargar ubicaciones de un usuario
    function cargarUbicacionesUsuario(usuarioId) {
        fetch(`/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/UsuarioController.php?action=getByUsuario&id=${usuarioId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(resultado => {
            if (resultado && resultado.success) {
                const ubicaciones = resultado.data;
                
                if (ubicaciones.length === 0) {
                    sinUbicacionesMsg.textContent = "Este usuario no tiene ubicaciones asignadas";
                    sinUbicacionesMsg.style.display = "block";
                    tablaUbicacionesContainer.style.display = "none";
                } else {
                    sinUbicacionesMsg.style.display = "none";
                    tablaUbicacionesContainer.style.display = "block";
                    
                    const tbody = tablaUbicaciones.querySelector('tbody');
                    tbody.innerHTML = '';
                    
                    ubicaciones.forEach(ubicacion => {
                        const tr = document.createElement('tr');
                        
                        // Tipo de ubicación con badge
                        const tdTipo = document.createElement('td');
                        let badgeClass = 'bg-secondary';
                        let tipoTexto = ubicacion.tipo_ubicacion;
                        
                        switch (ubicacion.tipo_ubicacion) {
                            case TIPO_HOSPITAL:
                                badgeClass = 'bg-primary';
                                tipoTexto = 'HOSPITAL/ALMACÉN';
                                break;
                            case TIPO_PLANTA:
                                badgeClass = 'bg-success';
                                tipoTexto = 'PLANTA';
                                break;
                            case TIPO_BOTIQUIN:
                                badgeClass = 'bg-info';
                                tipoTexto = 'BOTIQUÍN';
                                break;
                        }
                        
                        tdTipo.innerHTML = `<span class="badge ${badgeClass}">${tipoTexto}</span>`;
                        
                        // Nombre de ubicación (buscamos el nombre según el tipo e ID)
                        const tdNombre = document.createElement('td');
                        let nombreUbicacion = `ID: ${ubicacion.id_ubicacion}`;
                        
                        switch (ubicacion.tipo_ubicacion) {
                            case TIPO_HOSPITAL:
                                const almacen = almacenes.find(a => a.id == ubicacion.id_ubicacion);
                                if (almacen) nombreUbicacion = almacen.nombre;
                                break;
                            case TIPO_PLANTA:
                                const planta = plantas.find(p => p.id == ubicacion.id_ubicacion);
                                if (planta) nombreUbicacion = planta.nombre;
                                break;
                            case TIPO_BOTIQUIN:
                                const botiquin = botiquines.find(b => b.id == ubicacion.id_ubicacion);
                                if (botiquin) nombreUbicacion = botiquin.nombre;
                                break;
                        }
                        
                        tdNombre.textContent = nombreUbicacion;
                        
                        // Acciones
                        const tdAcciones = document.createElement('td');
                        tdAcciones.innerHTML = `
                            <button class="btn btn-sm btn-outline-danger btn-eliminar-ubicacion" 
                                data-usuario-id="${usuarioId}"
                                data-tipo="${ubicacion.tipo_ubicacion}"
                                data-id="${ubicacion.id_ubicacion}"
                                data-nombre="${tdNombre.textContent}">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        `;
                        
                        tr.appendChild(tdTipo);
                        tr.appendChild(tdNombre);
                        tr.appendChild(tdAcciones);
                        tbody.appendChild(tr);
                    });
                    
                    // Agregar eventos a los botones de eliminar
                    configurarBotonesEliminar();
                }
            } else {
                mostrarMensaje('error', 'Error al cargar ubicaciones: ' + 
                    (resultado ? resultado.message : 'Respuesta vacía'));
            }
        })
        .catch(error => {
            mostrarMensaje('error', 'Error en la solicitud: ' + error);
        });
    }
    
    // Configurar los botones de eliminar ubicación
    function configurarBotonesEliminar() {
        const botones = document.querySelectorAll('.btn-eliminar-ubicacion');
        
        botones.forEach(boton => {
            boton.addEventListener('click', function() {
                const usuarioId = this.dataset.usuarioId;
                const tipo = this.dataset.tipo;
                const id = this.dataset.id;
                const nombre = this.dataset.nombre;
                
                // Guardar datos temporalmente
                tempDeleteData = { usuarioId, tipo, id };
                
                // Mostrar modal de confirmación
                eliminarUbicacionNombre.textContent = `${getTipoTexto(tipo)}: ${nombre}`;
                eliminarUbicacionModal.show();
            });
        });
    }
    
    // Convertir tipo de ubicación a texto legible
    function getTipoTexto(tipo) {
        switch (tipo) {
            case TIPO_HOSPITAL: return 'HOSPITAL/ALMACÉN';
            case TIPO_PLANTA: return 'PLANTA';
            case TIPO_BOTIQUIN: return 'BOTIQUÍN';
            default: return tipo;
        }
    }
    
    // Configurar el botón de confirmar eliminación
    btnConfirmarEliminar.addEventListener('click', function() {
        if (!tempDeleteData) return;
        
        const { usuarioId, tipo, id } = tempDeleteData;
        
        // Llamar a la API para desasignar
        const formData = new FormData();
        formData.append('action', 'desasignar');
        formData.append('usuario_id', usuarioId);
        formData.append('tipo_ubicacion', tipo);
        formData.append('ubicacion_id', id);
        
        fetch('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/UsuarioController.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(resultado => {
            eliminarUbicacionModal.hide();
            
            if (resultado && resultado.success) {
                mostrarMensaje('success', 'Ubicación eliminada correctamente');
                cargarUbicacionesUsuario(usuarioId);
            } else {
                mostrarMensaje('error', 'Error: ' + (resultado ? resultado.message : 'No se pudo eliminar la ubicación'));
            }
        })
        .catch(error => {
            eliminarUbicacionModal.hide();
            mostrarMensaje('error', 'Error en la solicitud: ' + error);
        });
    });
    
    // Limpiar tabla de ubicaciones
    function limpiarTablaUbicaciones() {
        const tbody = tablaUbicaciones.querySelector('tbody');
        tbody.innerHTML = '';
    }
    
    // Mostrar mensajes de alerta
    function mostrarMensaje(tipo, mensaje) {
        const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const container = document.querySelector('.ubicaciones-container');
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-eliminar después de 5 segundos
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 300);
        }, 5000);
    }
});
</script>
