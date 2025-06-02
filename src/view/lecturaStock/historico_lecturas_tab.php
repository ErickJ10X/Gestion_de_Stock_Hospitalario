<?php
if (!isset($botiquines) || !isset($botiquinController) || !isset($productoController) || !isset($lecturas)) {
    echo '<p>Error: Datos necesarios no disponibles.</p>';
    exit;
}
?>
<div class="card-container">
    <div class="card-form">
        <h2>Histórico de Lecturas de Stock</h2>
        
        <div class="filtros-container">
            <form id="filtrosLecturas" class="filtros-form">
                <div class="form-group form-group--inline">
                    <label for="filtro-botiquin">Filtrar por botiquín:</label>
                    <select id="filtro-botiquin" class="filtro-select">
                        <option value="">Todos los botiquines</option>
                        <?php foreach ($botiquines as $botiquin): ?>
                            <option value="<?= $botiquin->getIdBotiquines() ?>">
                                <?= htmlspecialchars($botiquin->getNombre()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" id="btn-filtrar" class="btn-secondary">Filtrar</button>
                    <button type="button" id="btn-reset-filtros" class="btn-secondary">Limpiar filtros</button>
                </div>
            </form>
        </div>
        
        <div class="table-responsive">
            <table id="tabla-lecturas" class="list-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Botiquín</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Fecha de lectura</th>
                        <th>Registrado por</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($lecturas)): ?>
                        <?php foreach ($lecturas as $lectura): ?>
                            <?php
                                // Obtenemos el botiquín correspondiente
                                $botiquinResult = $botiquinController->show($lectura->getIdBotiquin());
                                $nombreBotiquin = !$botiquinResult['error'] && isset($botiquinResult['botiquin']) 
                                    ? $botiquinResult['botiquin']->getNombre() 
                                    : 'Desconocido';
                                
                                // Obtenemos el producto correspondiente
                                $productoResult = $productoController->show($lectura->getIdProducto());
                                $nombreProducto = !$productoResult['error'] && isset($productoResult['producto']) 
                                    ? $productoResult['producto']->getCodigo() . ' - ' . $productoResult['producto']->getNombre() 
                                    : 'Desconocido';
                            ?>
                            <tr data-botiquin="<?= $lectura->getIdBotiquin() ?>">
                                <td><?= $lectura->getIdLectura() ?></td>
                                <td><?= htmlspecialchars($nombreBotiquin) ?></td>
                                <td><?= htmlspecialchars($nombreProducto) ?></td>
                                <td><?= $lectura->getCantidadDisponible() ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($lectura->getFechaLectura())) ?></td>
                                <td><?= $lectura->getRegistradoPor() ?></td>
                                <td class="actions-cell">
                                    <a href="javascript:void(0)" class="btn-view btn-icon" title="Ver detalle"
                                       onclick="verDetalleLectura(<?= $lectura->getIdLectura() ?>)">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="no-results">No hay lecturas registradas</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para detalles de lectura -->
<div id="modal-detalle-lectura" class="modal">
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <h2>Detalle de Lectura de Stock</h2>
        <div id="detalle-lectura-content">
            <!-- El contenido se cargará dinámicamente -->
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtro por botiquín
    const filtroBotiquin = document.getElementById('filtro-botiquin');
    const btnFiltrar = document.getElementById('btn-filtrar');
    const btnResetFiltros = document.getElementById('btn-reset-filtros');
    const tablaLecturas = document.getElementById('tabla-lecturas');
    
    btnFiltrar.addEventListener('click', function() {
        const botiquinId = filtroBotiquin.value;
        const filas = tablaLecturas.querySelectorAll('tbody tr');
        
        filas.forEach(fila => {
            if (!botiquinId || fila.dataset.botiquin === botiquinId) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    });
    
    btnResetFiltros.addEventListener('click', function() {
        filtroBotiquin.value = '';
        const filas = tablaLecturas.querySelectorAll('tbody tr');
        filas.forEach(fila => {
            fila.style.display = '';
        });
    });
    
    // Modal de detalle
    const modal = document.getElementById('modal-detalle-lectura');
    const modalClose = document.querySelector('.modal-close');
    
    modalClose.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});

// Función para ver el detalle de una lectura
function verDetalleLectura(lecturaId) {
    const modal = document.getElementById('modal-detalle-lectura');
    const detalleContent = document.getElementById('detalle-lectura-content');
    
    // Fetch para obtener los detalles de la lectura
    fetch(`/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/routes/lecturasStock.routes.php?action=show&id=${lecturaId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                detalleContent.innerHTML = `<p class="error">${data.mensaje}</p>`;
            } else {
                const lectura = data.lectura;
                
                // Formato de la fecha
                const fechaObj = new Date(lectura.fecha_lectura);
                const fechaFormateada = fechaObj.toLocaleDateString('es-ES') + ' ' + fechaObj.toLocaleTimeString('es-ES');
                
                let html = `
                    <div class="detalle-lectura">
                        <p><strong>ID Lectura:</strong> ${lectura.id_lectura}</p>
                        <p><strong>Botiquín:</strong> ${lectura.nombre_botiquin}</p>
                        <p><strong>Producto:</strong> ${lectura.codigo_producto} - ${lectura.nombre_producto}</p>
                        <p><strong>Cantidad disponible:</strong> ${lectura.cantidad_disponible}</p>
                        <p><strong>Fecha de lectura:</strong> ${fechaFormateada}</p>
                        <p><strong>Registrado por:</strong> ${lectura.nombre_usuario || lectura.registrado_por}</p>
                        <p><strong>Unidad de medida:</strong> ${lectura.unidad_medida}</p>
                    </div>
                `;
                
                detalleContent.innerHTML = html;
            }
            
            modal.style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            detalleContent.innerHTML = `<p class="error">Error al cargar los detalles de la lectura</p>`;
            modal.style.display = 'block';
        });
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

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: #fff;
    margin: 10% auto;
    padding: 20px;
    border-radius: 5px;
    width: 70%;
    max-width: 600px;
    position: relative;
}

.modal-close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
}

.detalle-lectura {
    margin-top: 15px;
}

.error {
    color: #ff0000;
}

.no-results {
    text-align: center;
    padding: 20px;
    color: #666;
}
</style>
