<div class="catalogo-productos-container">
    <div class="filter-section">
        <div class="form-group">
            <label for="planta-select">Seleccionar Planta:</label>
            <select id="planta-select" class="form-control">
                <option value="">Seleccione una planta</option>
                <?php foreach ($plantas as $planta): ?>
                    <option value="<?= $planta->getIdPlanta() ?>"><?= htmlspecialchars($planta->getNombre()) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="table-container" id="productos-planta-container">
        <div class="no-data-message" id="no-planta-message">
            Seleccione una planta para ver su catálogo de productos
        </div>
        
        <table class="list-table" id="productos-planta-table" style="display: none;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Fabricante</th>
                    <th>Referencia</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="productos-planta-body">
                <!-- Los productos se cargarán dinámicamente aquí -->
            </tbody>
        </table>
    </div>

    <div id="error-message" class="error-message" style="display: none;"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const plantaSelect = document.getElementById('planta-select');
    const productosTable = document.getElementById('productos-planta-table');
    const productosBody = document.getElementById('productos-planta-body');
    const noPlantaMessage = document.getElementById('no-planta-message');
    const errorMessage = document.getElementById('error-message');

    plantaSelect.addEventListener('change', function() {
        const plantaId = this.value;
        
        if (!plantaId) {
            productosTable.style.display = 'none';
            noPlantaMessage.style.display = 'block';
            errorMessage.style.display = 'none';
            return;
        }

        // Mostrar carga
        noPlantaMessage.textContent = 'Cargando productos...';
        noPlantaMessage.style.display = 'block';
        productosTable.style.display = 'none';
        errorMessage.style.display = 'none';

        // Realizar la petición AJAX
        fetch(`/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/api/catalogos_productos.php?planta_id=${plantaId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    errorMessage.textContent = data.mensaje || 'Error al cargar los productos';
                    errorMessage.style.display = 'block';
                    noPlantaMessage.style.display = 'none';
                    productosTable.style.display = 'none';
                    return;
                }

                // Limpiar tabla
                productosBody.innerHTML = '';
                
                // Si no hay productos
                if (!data.productos || data.productos.length === 0) {
                    noPlantaMessage.textContent = 'No hay productos en el catálogo de esta planta';
                    noPlantaMessage.style.display = 'block';
                    productosTable.style.display = 'none';
                    return;
                }

                // Llenar tabla con productos
                data.productos.forEach(producto => {
                    const row = document.createElement('tr');
                    
                    row.innerHTML = `
                        <td>${producto.id_producto}</td>
                        <td>${producto.nombre}</td>
                        <td>${producto.descripcion || ''}</td>
                        <td>${producto.fabricante || ''}</td>
                        <td>${producto.referencia || ''}</td>
                        <td>
                            <div class="list-actions">
                                <a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/detalle_producto.php?id=${producto.id_producto}" class="list-action list-action--view">Ver</a>
                                <button class="list-action list-action--delete" data-catalogo-id="${producto.id_catalogo}" data-producto-id="${producto.id_producto}" data-producto-nombre="${producto.nombre}">Eliminar del catálogo</button>
                            </div>
                        </td>
                    `;
                    
                    productosBody.appendChild(row);
                });

                // Configurar eventos para botones de eliminar
                document.querySelectorAll('.list-action--delete').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const catalogoId = this.getAttribute('data-catalogo-id');
                        const productoId = this.getAttribute('data-producto-id');
                        const productoNombre = this.getAttribute('data-producto-nombre');
                        
                        if (confirm(`¿Está seguro que desea eliminar el producto "${productoNombre}" del catálogo de esta planta?`)) {
                            eliminarProductoCatalogo(catalogoId, plantaId);
                        }
                    });
                });
                
                // Mostrar la tabla
                noPlantaMessage.style.display = 'none';
                productosTable.style.display = 'table';
            })
            .catch(error => {
                errorMessage.textContent = 'Error de conexión al servidor';
                errorMessage.style.display = 'block';
                noPlantaMessage.style.display = 'none';
                productosTable.style.display = 'none';
                console.error('Error:', error);
            });
    });

    function eliminarProductoCatalogo(catalogoId, plantaId) {
        fetch('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/api/catalogos_productos.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete&catalogo_id=${catalogoId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.mensaje || 'Error al eliminar el producto del catálogo');
            } else {
                alert(data.mensaje || 'Producto eliminado del catálogo correctamente');
                // Recargar la lista
                plantaSelect.dispatchEvent(new Event('change'));
            }
        })
        .catch(error => {
            alert('Error de conexión al servidor');
            console.error('Error:', error);
        });
    }
});
</script>
