<?php
if (!isset($botiquines) || !isset($botiquinController) || !isset($productoController)) {
    echo '<p>Error: Datos necesarios no disponibles.</p>';
    exit;
}
?>

<div class="card-container">
    <div class="card-form">
        <h2>Próximas Lecturas de Stock</h2>
        
        <div class="filtros-container">
            <form id="filtrosProximasLecturas" class="filtros-form">
                <div class="form-group form-group--inline">
                    <label for="filtro-botiquin-proximas">Filtrar por botiquín:</label>
                    <select id="filtro-botiquin-proximas" class="filtro-select">
                        <option value="">Todos los botiquines</option>
                        <?php foreach ($botiquines as $botiquin): ?>
                            <option value="<?= $botiquin->getIdBotiquines() ?>">
                                <?= htmlspecialchars($botiquin->getNombre()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" id="btn-filtrar-proximas" class="btn-secondary">Filtrar</button>
                    <button type="button" id="btn-reset-filtros-proximas" class="btn-secondary">Limpiar filtros</button>
                </div>
            </form>
        </div>
        
        <div class="table-responsive">
            <table id="tabla-proximas-lecturas" class="list-table">
                <thead>
                    <tr>
                        <th>Botiquín</th>
                        <th>Producto</th>
                        <th>Última lectura</th>
                        <th>Cantidad actual</th>
                        <th>Fecha próxima lectura</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="proximas-lecturas-body">
                    <tr>
                        <td colspan="7" class="loading-message">Cargando datos de próximas lecturas...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cargar las próximas lecturas al cargar la página
    cargarProximasLecturas();
    
    // Filtro por botiquín
    const filtroBotiquin = document.getElementById('filtro-botiquin-proximas');
    const btnFiltrar = document.getElementById('btn-filtrar-proximas');
    const btnResetFiltros = document.getElementById('btn-reset-filtros-proximas');
    
    btnFiltrar.addEventListener('click', function() {
        cargarProximasLecturas(filtroBotiquin.value);
    });
    
    btnResetFiltros.addEventListener('click', function() {
        filtroBotiquin.value = '';
        cargarProximasLecturas();
    });
    
    // Función para cargar las próximas lecturas
    function cargarProximasLecturas(botiquinId = '') {
        const tbody = document.getElementById('proximas-lecturas-body');
        
        // Mostrar mensaje de carga
        tbody.innerHTML = '<tr><td colspan="7" class="loading-message">Cargando datos de próximas lecturas...</td></tr>';
        
        // Construir URL con parámetro de filtro si existe
        let url = '/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/routes/lecturasStock.routes.php?action=getProximasLecturas';
        if (botiquinId) {
            url += `&botiquin_id=${botiquinId}`;
        }
        
        // Realizar la petición fetch
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    tbody.innerHTML = `<tr><td colspan="7" class="error-message">${data.mensaje}</td></tr>`;
                } else if (!data.lecturas || data.lecturas.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" class="no-results">No hay próximas lecturas programadas</td></tr>';
                } else {
                    // Limpiar la tabla
                    tbody.innerHTML = '';
                    
                    // Llenar con los datos recibidos
                    data.lecturas.forEach(lectura => {
                        // Calcular el estado basado en la fecha próxima
                        let estado = '';
                        let estadoClass = '';
                        
                        const hoy = new Date();
                        const fechaProxima = new Date(lectura.fecha_proxima_lectura);
                        
                        if (fechaProxima < hoy) {
                            estado = 'Atrasada';
                            estadoClass = 'estado-atrasada';
                        } else {
                            // Calcular días de diferencia
                            const diffDays = Math.ceil((fechaProxima - hoy) / (1000 * 60 * 60 * 24));
                            
                            if (diffDays <= 2) {
                                estado = 'Urgente';
                                estadoClass = 'estado-urgente';
                            } else if (diffDays <= 7) {
                                estado = 'Próxima';
                                estadoClass = 'estado-proxima';
                            } else {
                                estado = 'Programada';
                                estadoClass = 'estado-programada';
                            }
                        }
                        
                        // Formatear fechas
                        const fechaUltimaLectura = new Date(lectura.ultima_fecha_lectura);
                        const fechaUltimaFormateada = fechaUltimaLectura.toLocaleDateString('es-ES');
                        
                        const fechaProximaFormateada = fechaProxima.toLocaleDateString('es-ES');
                        
                        // Crear fila
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td data-botiquin-id="${lectura.id_botiquin}">${lectura.nombre_botiquin}</td>
                            <td>${lectura.codigo_producto} - ${lectura.nombre_producto}</td>
                            <td>${fechaUltimaFormateada}</td>
                            <td>${lectura.cantidad_disponible}</td>
                            <td>${fechaProximaFormateada}</td>
                            <td><span class="estado-badge ${estadoClass}">${estado}</span></td>
                            <td class="actions-cell">
                                <button class="btn-primary btn-registrar-lectura" 
                                        data-botiquin-id="${lectura.id_botiquin}"
                                        data-producto-id="${lectura.id_producto}"
                                        onclick="registrarNuevaLectura(${lectura.id_botiquin}, ${lectura.id_producto})">
                                    Registrar lectura
                                </button>
                            </td>
                        `;
                        
                        tbody.appendChild(tr);
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tbody.innerHTML = '<tr><td colspan="7" class="error-message">Error al cargar los datos</td></tr>';
            });
    }
});

// Función para ir a la pestaña de registrar lectura con datos precargados
function registrarNuevaLectura(botiquinId, productoId) {
    // Cambiar a la pestaña de registrar lectura
    document.querySelector('.tab-btn[data-tab="tab-registrar-lectura"]').click();
    
    // Esperar a que la pestaña se muestre y luego preseleccionar los valores
    setTimeout(() => {
        // Seleccionar el botiquín
        const botiquinSelect = document.getElementById('id_botiquin');
        if (botiquinSelect) {
            botiquinSelect.value = botiquinId;
            
            // Disparar el evento change para cargar los productos
            const event = new Event('change');
            botiquinSelect.dispatchEvent(event);
            
            // Esperar a que los productos se carguen y luego seleccionar el producto
            setTimeout(() => {
                const productoSelect = document.getElementById('id_producto');
                if (productoSelect) {
                    productoSelect.value = productoId;
                }
            }, 500); // Dar tiempo para que se carguen los productos
        }
    }, 100);
}
</script>

<style>
.filtros-container {
    margin-bottom: 20px;
    background-color: #f9f9f9;
    padding: 15px;
    border-radius: 5px;
}

.filtros-form {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.form-group--inline {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filtro-select {
    height: 35px;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.table-responsive {
    overflow-x: auto;
}

.actions-cell {
    white-space: nowrap;
    text-align: center;
}

.loading-message {
    text-align: center;
    padding: 20px;
    color: #666;
}

.error-message {
    text-align: center;
    padding: 20px;
    color: #d9534f;
}

.no-results {
    text-align: center;
    padding: 20px;
    color: #666;
}

.estado-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.estado-atrasada {
    background-color: #d9534f;
    color: white;
}

.estado-urgente {
    background-color: #f0ad4e;
    color: white;
}

.estado-proxima {
    background-color: #5bc0de;
    color: white;
}

.estado-programada {
    background-color: #5cb85c;
    color: white;
}

.btn-registrar-lectura {
    padding: 4px 8px;
    font-size: 12px;
}
</style>
