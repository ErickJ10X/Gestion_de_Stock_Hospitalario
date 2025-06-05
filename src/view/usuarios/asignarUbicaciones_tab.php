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
                <div class="section-title bg-primary">
                    Asignar Nueva Ubicación
                </div>
                <div class="card-body">
                    <form id="formAsignarUbicacion" class="card-form" autocomplete="off">
                        <!-- Información de usuario y ubicación -->
                        <div class="row mb-2">
                            <div class="col-12">
                                <h5 class="form-section-title text-primary"><i class="fas fa-user-tag"></i> Selección de Usuario</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
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
                        </div>

                        <!-- Sección de ubicación -->
                        <div class="row mt-3 mb-2">
                            <div class="col-12">
                                <h5 class="form-section-title text-primary"><i class="fas fa-map-marker-alt"></i> Datos de Ubicación</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="tipo_ubicacion" class="form-label">Tipo de ubicación <span class="text-danger">*</span></label>
                                    <select id="tipo_ubicacion" name="tipo_ubicacion" class="form-select" required>
                                        <option value="">Seleccionar tipo</option>
                                        <option value="Hospital">Hospital/Almacén</option>
                                        <option value="Planta">Planta</option>
                                        <option value="Botiquin">Botiquín</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="id_ubicacion" class="form-label">Ubicación <span class="text-danger">*</span></label>
                                    <select id="id_ubicacion" name="id_ubicacion" class="form-select" required disabled>
                                        <option value="">Seleccione un tipo primero</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mt-4 text-center">
                            <button type="submit" class="btn btn-primary" id="btnAsignar">
                                <i class="fas fa-plus-circle"></i> Asignar Ubicación
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
                <div class="section-title bg-info">
                    Ubicaciones Asignadas
                </div>
                <div class="card-body">
                    <!-- Selector de usuario -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="verUsuarioUbicaciones" class="form-label">Seleccione un usuario</label>
                                <select id="verUsuarioUbicaciones" class="form-select">
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
                    </div>

                    <!-- Mensaje de selección inicial -->
                    <div id="sinUbicacionesMsg" class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i> Seleccione un usuario para ver sus ubicaciones asignadas
                    </div>

                    <!-- Tabla de ubicaciones oculta inicialmente -->
                    <div id="tablaUbicacionesContainer" style="display: none;">
                        <div class="row mb-2">
                            <div class="col-12">
                                <h5 class="form-section-title text-info"><i class="fas fa-list"></i> Ubicaciones del Usuario</h5>
                            </div>
                        </div>
                        
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
                                    <!-- Las ubicaciones se cargarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Exportar datos para JavaScript -->
<script>
// Datos de ubicaciones para JS
window.datosUbicaciones = {
    almacenes: <?= json_encode(array_map(function($a) { return ['id' => $a->getId(), 'nombre' => $a->getTipo()]; }, $almacenes)) ?>,
    plantas: <?= json_encode(array_map(function($p) { return ['id' => $p->getIdPlanta(), 'nombre' => $p->getNombre()]; }, $plantas)) ?>,
    botiquines: <?= json_encode(array_map(function($b) { return ['id' => $b->getId(), 'nombre' => $b->getNombre()]; }, $botiquines)) ?>
};

document.addEventListener('DOMContentLoaded', function() {
    // Variables para los elementos del DOM
    const formAsignarUbicacion = document.getElementById('formAsignarUbicacion');
    const usuarioSelect = document.getElementById('usuario_id');
    const tipoUbicacionSelect = document.getElementById('tipo_ubicacion');
    const ubicacionSelect = document.getElementById('id_ubicacion');
    const sinUbicacionesMsg = document.getElementById('sinUbicacionesMsg');
    const tablaUbicacionesContainer = document.getElementById('tablaUbicacionesContainer');
    const tablaUbicaciones = document.getElementById('tablaUbicaciones');
    const btnLimpiarUbicacion = document.getElementById('btnLimpiarUbicacion');
    const verUsuarioUbicaciones = document.getElementById('verUsuarioUbicaciones');

    // Toggle entre formularios
    document.querySelectorAll('.form-toggle-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.form-toggle-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');

            const target = this.getAttribute('data-target');
            document.querySelectorAll('.form-section').forEach(section => {
                section.style.display = 'none';
            });
            document.getElementById(target).style.display = 'block';
        });
    });

    // Limpiar select de ubicación al limpiar formulario
    if (btnLimpiarUbicacion) {
        btnLimpiarUbicacion.addEventListener('click', function() {
            ubicacionSelect.innerHTML = '<option value="">Seleccione un tipo primero</option>';
            ubicacionSelect.disabled = true;
            tipoUbicacionSelect.value = '';
        });
    }

    // Cambiar opciones de ubicación según tipo
    tipoUbicacionSelect.addEventListener('change', function() {
        const tipo = this.value;
        ubicacionSelect.innerHTML = '<option value="">Seleccione una ubicación</option>';
        ubicacionSelect.disabled = true;

        let ubicaciones = [];
        if (tipo === 'Hospital') {
            ubicaciones = window.datosUbicaciones.almacenes;
        } else if (tipo === 'Planta') {
            ubicaciones = window.datosUbicaciones.plantas;
        } else if (tipo === 'Botiquin') {
            ubicaciones = window.datosUbicaciones.botiquines;
        }

        if (ubicaciones.length > 0) {
            ubicacionSelect.disabled = false;
            ubicaciones.forEach(ubicacion => {
                const option = document.createElement('option');
                option.value = ubicacion.id;
                option.textContent = ubicacion.nombre;
                ubicacionSelect.appendChild(option);
            });
        }
    });

    // Al cambiar usuario en el formulario de ver ubicaciones
    verUsuarioUbicaciones.addEventListener('change', function() {
        limpiarTablaUbicaciones();
        const usuarioId = this.value;
        if (usuarioId) {
            cargarUbicacionesUsuario(usuarioId);
        } else {
            sinUbicacionesMsg.innerHTML = "<i class='fas fa-info-circle me-2'></i> Seleccione un usuario para ver sus ubicaciones asignadas";
            sinUbicacionesMsg.style.display = "block";
            tablaUbicacionesContainer.style.display = "none";
        }
    });

    // Enviar formulario de asignación
    formAsignarUbicacion.addEventListener('submit', function(e) {
        e.preventDefault();

        const usuarioId = usuarioSelect.value;
        const tipoUbicacion = tipoUbicacionSelect.value;
        const ubicacionId = ubicacionSelect.value;

        if (!usuarioId || !tipoUbicacion || !ubicacionId) {
            mostrarMensaje('error', 'Todos los campos son obligatorios');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'asignar');
        formData.append('usuario_id', usuarioId);
        formData.append('tipo_ubicacion', tipoUbicacion);
        formData.append('ubicacion_id', ubicacionId);

        fetch('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/UsuarioController.php', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(response => response.json())
        .then(resultado => {
            if (resultado && resultado.success) {
                mostrarMensaje('success', 'Ubicación asignada correctamente');
                formAsignarUbicacion.reset();
                ubicacionSelect.innerHTML = '<option value="">Seleccione un tipo primero</option>';
                ubicacionSelect.disabled = true;
                
                // Si está seleccionado el mismo usuario en la vista de ubicaciones, actualizar
                if (verUsuarioUbicaciones.value === usuarioId) {
                    cargarUbicacionesUsuario(usuarioId);
                }
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
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(resultado => {
            if (resultado && resultado.success) {
                const ubicaciones = resultado.data;

                if (!ubicaciones || ubicaciones.length === 0) {
                    sinUbicacionesMsg.innerHTML = "<i class='fas fa-info-circle me-2'></i> Este usuario no tiene ubicaciones asignadas";
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
                            case 'Hospital':
                                badgeClass = 'bg-primary';
                                tipoTexto = 'HOSPITAL/ALMACÉN';
                                break;
                            case 'Planta':
                                badgeClass = 'bg-success';
                                tipoTexto = 'PLANTA';
                                break;
                            case 'Botiquin':
                                badgeClass = 'bg-info';
                                tipoTexto = 'BOTIQUÍN';
                                break;
                        }

                        tdTipo.innerHTML = `<span class="badge ${badgeClass}">${tipoTexto}</span>`;

                        // Nombre de ubicación
                        const tdNombre = document.createElement('td');
                        let nombreUbicacion = `ID: ${ubicacion.id_ubicacion}`;
                        
                        if (ubicacion.tipo_ubicacion === 'Hospital') {
                            const almacen = window.datosUbicaciones.almacenes.find(a => a.id == ubicacion.id_ubicacion);
                            if (almacen) nombreUbicacion = almacen.nombre;
                        } else if (ubicacion.tipo_ubicacion === 'Planta') {
                            const planta = window.datosUbicaciones.plantas.find(p => p.id == ubicacion.id_ubicacion);
                            if (planta) nombreUbicacion = planta.nombre;
                        } else if (ubicacion.tipo_ubicacion === 'Botiquin') {
                            const botiquin = window.datosUbicaciones.botiquines.find(b => b.id == ubicacion.id_ubicacion);
                            if (botiquin) nombreUbicacion = botiquin.nombre;
                        }
                        
                        tdNombre.textContent = nombreUbicacion;

                        // Acciones
                        const tdAcciones = document.createElement('td');
                        tdAcciones.innerHTML = `
                            <button class="btn btn-sm btn-outline-danger btn-eliminar-ubicacion" 
                                data-usuario-id="${usuarioId}"
                                data-tipo="${ubicacion.tipo_ubicacion}"
                                data-id="${ubicacion.id_ubicacion}"
                                data-nombre="${nombreUbicacion}">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        `;

                        tr.appendChild(tdTipo);
                        tr.appendChild(tdNombre);
                        tr.appendChild(tdAcciones);
                        tbody.appendChild(tr);
                    });

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

                tempDeleteData = { usuarioId, tipo, id };

                eliminarUbicacionNombre.textContent = `${getTipoTexto(tipo)}: ${nombre}`;
                eliminarUbicacionModal.show();
            });
        });
    }

    function getTipoTexto(tipo) {
        switch (tipo) {
            case 'Hospital': return 'HOSPITAL/ALMACÉN';
            case 'Planta': return 'PLANTA';
            case 'Botiquin': return 'BOTIQUÍN';
            default: return tipo;
        }
    }

    btnConfirmarEliminar.addEventListener('click', function() {
        if (!tempDeleteData) return;

        const { usuarioId, tipo, id } = tempDeleteData;

        const formData = new FormData();
        formData.append('action', 'desasignar');
        formData.append('usuario_id', usuarioId);
        formData.append('tipo_ubicacion', tipo);
        formData.append('ubicacion_id', id);

        fetch('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/UsuarioController.php', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
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

    function limpiarTablaUbicaciones() {
        const tbody = tablaUbicaciones.querySelector('tbody');
        tbody.innerHTML = '';
    }

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

        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 300);
        }, 5000);
    }

    // Variable temporal para eliminación
    let tempDeleteData = null;
});
</script>
