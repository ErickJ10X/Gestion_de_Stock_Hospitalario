<?php
if(!isset($reposicionesController) || !isset($almacenes) || !isset($productos) || !isset($botiquines)) {
    die('No se puede acceder directamente a este archivo.');
}
?>

<div class="tab-header">
    <h2>Marcar Reposiciones como Entregadas</h2>
</div>

<div class="entrega-container">
    <div class="card shadow mb-4">
        <div class="card-header">
            <h3>Seleccione Reposiciones Pendientes</h3>
        </div>
        <div class="card-body">
            <!-- Filtros de búsqueda -->
            <div class="filtros-form mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filtro-botiquin-entrega">Filtrar por botiquín:</label>
                            <select id="filtro-botiquin-entrega" class="form-select">
                                <option value="">Todos los botiquines</option>
                                <?php foreach ($botiquines as $botiquin): ?>
                                    <option value="<?= $botiquin->getIdBotiquines() ?>"><?= htmlspecialchars($botiquin->getNombre()) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filtro-urgencia-entrega">Filtrar por urgencia:</label>
                            <select id="filtro-urgencia-entrega" class="form-select">
                                <option value="">Todos</option>
                                <option value="1">Solo Urgentes</option>
                                <option value="0">Solo Normales</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="d-grid">
                                <button id="buscar-pendientes" class="btn btn-primary">Buscar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de reposiciones pendientes -->
            <div class="table-responsive">
                <form id="form-entregas" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/api/reposiciones_update.php" method="POST">
                    <input type="hidden" name="action" value="marcar_entregadas">
                    
                    <table class="list-table" id="tablaPendientes">
                        <thead>
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="seleccionar-todas">
                                    </div>
                                </th>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Origen</th>
                                <th>Destino</th>
                                <th>Cantidad</th>
                                <th>Fecha</th>
                                <th>Urgencia</th>
                            </tr>
                        </thead>
                        <tbody id="pendientes-tbody">
                            <tr>
                                <td colspan="8" class="text-center">Utilice los filtros para buscar reposiciones pendientes</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="mt-4 text-center" id="entrega-actions" style="display:none;">
                        <div class="form-group mb-3">
                            <label for="fecha_entrega" class="form-label">Fecha y Hora de Entrega</label>
                            <input type="datetime-local" id="fecha_entrega" name="fecha_entrega" class="form-control form-control-inline" style="max-width: 300px;">
                            <small class="text-muted">Deje en blanco para usar la fecha actual</small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="notas_entrega" class="form-label">Notas de Entrega</label>
                            <textarea id="notas_entrega" name="notas_entrega" class="form-control" style="max-width: 500px; margin: 0 auto;" rows="3" placeholder="Observaciones sobre la entrega (opcional)"></textarea>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" id="btn-confirmar-entrega" class="btn btn-success">
                                <i class="fas fa-check-circle"></i> Confirmar Entrega
                            </button>
                            <button type="reset" id="btn-cancelar-entrega" class="btn btn-secondary">
                                <i class="fas fa-times-circle"></i> Cancelar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formEntregas = document.getElementById('form-entregas');
    const entregaActions = document.getElementById('entrega-actions');
    const selectTodas = document.getElementById('seleccionar-todas');
    const btnBuscarPendientes = document.getElementById('buscar-pendientes');
    const filtroBotiquin = document.getElementById('filtro-botiquin-entrega');
    const filtroUrgencia = document.getElementById('filtro-urgencia-entrega');
    const pendientesTbody = document.getElementById('pendientes-tbody');
    
    // Buscar reposiciones pendientes
    btnBuscarPendientes.addEventListener('click', function() {
        // Mostrar indicador de carga
        pendientesTbody.innerHTML = '<tr><td colspan="8" class="text-center">Cargando reposiciones pendientes...</td></tr>';
        
        // Construir la URL para la petición
        const botiquinId = filtroBotiquin.value;
        const urgencia = filtroUrgencia.value;
        let url = '/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/api/reposiciones_api.php?action=get_pendientes';
        
        if (botiquinId) {
            url += `&botiquin=${botiquinId}`;
        }
        
        if (urgencia !== '') {
            url += `&urgente=${urgencia}`;
        }
        
        // Realizar la petición
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    pendientesTbody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">${data.mensaje}</td></tr>`;
                } else if (!data.reposiciones || data.reposiciones.length === 0) {
                    pendientesTbody.innerHTML = '<tr><td colspan="8" class="text-center">No se encontraron reposiciones pendientes con los filtros seleccionados</td></tr>';
                    entregaActions.style.display = 'none';
                } else {
                    // Generar filas de la tabla
                    pendientesTbody.innerHTML = '';
                    data.reposiciones.forEach(repo => {
                        const row = document.createElement('tr');
                        const urgenciaClass = repo.urgente ? 'badge bg-danger' : 'badge bg-primary';
                        const urgenciaText = repo.urgente ? 'URGENTE' : 'Normal';
                        
                        row.innerHTML = `
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input checkbox-reposicion" type="checkbox" name="ids_reposicion[]" value="${repo.id}">
                                </div>
                            </td>
                            <td>${repo.id}</td>
                            <td>${repo.producto_nombre}</td>
                            <td>${repo.almacen_nombre}</td>
                            <td>${repo.botiquin_nombre}</td>
                            <td>${repo.cantidad}</td>
                            <td>${formatDate(repo.fecha)}</td>
                            <td><span class="${urgenciaClass}">${urgenciaText}</span></td>
                        `;
                        
                        pendientesTbody.appendChild(row);
                    });
                    
                    // Mostrar acciones de entrega
                    entregaActions.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                pendientesTbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error al cargar las reposiciones pendientes</td></tr>';
            });
    });
    
    // Manejar selección de todas las reposiciones
    selectTodas.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.checkbox-reposicion');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // Formato de fecha
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    // Validación del formulario
    formEntregas.addEventListener('submit', function(event) {
        const checkboxes = document.querySelectorAll('.checkbox-reposicion:checked');
        
        if (checkboxes.length === 0) {
            alert('Debe seleccionar al menos una reposición para marcarla como entregada');
            event.preventDefault();
            return;
        }
        
        // Aquí se podría agregar una confirmación personalizada
        if (!confirm('¿Está seguro que desea marcar como entregadas las reposiciones seleccionadas?')) {
            event.preventDefault();
        }
    });
    
    // Manejar cancelación
    document.getElementById('btn-cancelar-entrega').addEventListener('click', function() {
        // Deseleccionar todas las casillas
        selectTodas.checked = false;
        document.querySelectorAll('.checkbox-reposicion').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Ocultar acciones
        entregaActions.style.display = 'none';
    });
});
</script>

<style>
.entrega-container {
    margin-bottom: 30px;
}

.filtros-form {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
}

.form-control-inline {
    width: auto;
    display: inline-block;
    margin: 0 auto;
}
</style>
