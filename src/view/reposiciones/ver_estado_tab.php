<?php
if(!isset($reposicionesController) || !isset($almacenes) || !isset($productos) || !isset($botiquines)) {
    die('No se puede acceder directamente a este archivo.');
}
?>

<div class="tab-header">
    <h2>Estado de Reposiciones</h2>
    <div class="tab-actions">
        <div class="filtros-container">
            <div class="filtro-group">
                <label for="filtro-urgencia">Filtrar por urgencia:</label>
                <select id="filtro-urgencia" class="form-select">
                    <option value="todos">Todos</option>
                    <option value="urgente">Solo Urgentes</option>
                    <option value="normal">Solo Normales</option>
                </select>
            </div>
            <div class="filtro-group">
                <label for="filtro-botiquin">Filtrar por botiquín:</label>
                <select id="filtro-botiquin" class="form-select">
                    <option value="todos">Todos los botiquines</option>
                    <?php foreach ($botiquines as $botiquin): ?>
                        <option value="<?= $botiquin->getIdBotiquines() ?>"><?= htmlspecialchars($botiquin->getNombre()) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filtro-group">
                <button id="aplicar-filtros" class="btn btn-primary">Aplicar Filtros</button>
                <button id="limpiar-filtros" class="btn btn-secondary">Limpiar Filtros</button>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="list-table" id="tablaReposiciones">
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Desde Almacén</th>
                <th>Hasta Botiquín</th>
                <th>Cantidad</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Urgencia</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reposiciones)): ?>
                <tr>
                    <td colspan="8" class="list-table__empty">No hay reposiciones registradas</td>
                </tr>
            <?php else: ?>
                <?php foreach ($reposiciones as $reposicion): 
                    // Buscar nombre del producto
                    $nombreProducto = "Producto no encontrado";
                    foreach ($productos as $producto) {
                        if ($producto->getIdProducto() == $reposicion->getIdProducto()) {
                            $nombreProducto = $producto->getNombre();
                            break;
                        }
                    }
                    
                    // Buscar nombre del almacén
                    $nombreAlmacen = "Almacén no encontrado";
                    foreach ($almacenes as $almacen) {
                        if ($almacen->getIdAlmacen() == $reposicion->getDesdeAlmacen()) {
                            $nombreAlmacen = $almacen->getTipo();
                            break;
                        }
                    }
                    
                    // Buscar nombre del botiquín
                    $nombreBotiquin = "Botiquín no encontrado";
                    foreach ($botiquines as $botiquin) {
                        if ($botiquin->getIdBotiquines() == $reposicion->getHastaBotiquin()) {
                            $nombreBotiquin = $botiquin->getNombre();
                            break;
                        }
                    }
                    
                    // Determinar el estado (esto es un ejemplo, ajustar según lógica real)
                    $estado = "Pendiente"; // Por defecto, todas están pendientes
                    
                    // Determinar la clase de urgencia
                    $urgenciaClass = $reposicion->getUrgente() ? "badge bg-danger" : "badge bg-primary";
                    $urgenciaText = $reposicion->getUrgente() ? "URGENTE" : "Normal";
                ?>
                    <tr class="list-table__body-row">
                        <td class="list-table__body-cell"><?= $reposicion->getIdReposicion() ?></td>
                        <td class="list-table__body-cell"><?= htmlspecialchars($nombreProducto) ?></td>
                        <td class="list-table__body-cell"><?= htmlspecialchars($nombreAlmacen) ?></td>
                        <td class="list-table__body-cell"><?= htmlspecialchars($nombreBotiquin) ?></td>
                        <td class="list-table__body-cell"><?= $reposicion->getCantidadRepuesta() ?></td>
                        <td class="list-table__body-cell"><?= date('d/m/Y H:i', strtotime($reposicion->getFecha())) ?></td>
                        <td class="list-table__body-cell">
                            <span class="badge bg-warning"><?= $estado ?></span>
                        </td>
                        <td class="list-table__body-cell">
                            <span class="<?= $urgenciaClass ?>"><?= $urgenciaText ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTable
    if (typeof $.fn.DataTable !== 'undefined') {
        const tabla = $('#tablaReposiciones').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
            },
            order: [[5, 'desc']], // Ordenar por fecha por defecto
            responsive: true
        });
        
        // Manejar filtros adicionales
        document.getElementById('aplicar-filtros').addEventListener('click', function() {
            const filtroUrgencia = document.getElementById('filtro-urgencia').value;
            const filtroBotiquin = document.getElementById('filtro-botiquin').value;
            
            tabla.columns(7).search(filtroUrgencia === 'urgente' ? 'URGENTE' : 
                                    filtroUrgencia === 'normal' ? 'Normal' : '').draw();
            
            if (filtroBotiquin !== 'todos') {
                // Aquí tendríamos que implementar una búsqueda más compleja
                // ya que DataTables no busca por atributos data-*
                tabla.column(3).search(filtroBotiquin).draw();
            }
        });
        
        document.getElementById('limpiar-filtros').addEventListener('click', function() {
            document.getElementById('filtro-urgencia').value = 'todos';
            document.getElementById('filtro-botiquin').value = 'todos';
            tabla.search('').columns().search('').draw();
        });
    } else {
        console.warn('DataTables no está disponible');
    }
});
</script>

<style>
.tab-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.tab-header h2 {
    margin: 0;
}

.filtros-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 5px;
}

.filtro-group {
    display: flex;
    flex-direction: column;
    min-width: 200px;
}

.filtro-group label {
    margin-bottom: 5px;
    font-weight: 500;
}
</style>
