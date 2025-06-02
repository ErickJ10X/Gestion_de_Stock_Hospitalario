<?php 
if(!isset($botiquines) || empty($botiquines)) {
    echo '<div class="alert alert-warning">No se han encontrado botiquines. Por favor, asegúrese de que existen botiquines registrados.</div>';
}
?>

<div class="card-container">
    <div class="card-form">
        <h2>Registrar nueva lectura de stock</h2>
        
        <form id="formRegistrarLectura" action="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/routes/lecturasStock.routes.php" method="POST">
            <input type="hidden" name="action" value="store">
            <input type="hidden" name="registrado_por" value="<?= $_SESSION['user_id'] ?? 0 ?>">
            
            <div class="form-group">
                <label for="id_botiquin">Botiquín:</label>
                <select id="id_botiquin" name="id_botiquin" required <?= empty($botiquines) ? 'disabled' : '' ?>>
                    <option value="">Seleccione un botiquín</option>
                    <?php if(isset($botiquines) && !empty($botiquines)): ?>
                        <?php foreach ($botiquines as $botiquin): ?>
                            <option value="<?= $botiquin->getIdBotiquines() ?>">
                                <?= htmlspecialchars($botiquin->getNombre()) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if(empty($botiquines)): ?>
                    <small class="error-text">No hay botiquines disponibles</small>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="id_producto">Producto:</label>
                <select id="id_producto" name="id_producto" required disabled>
                    <option value="">Seleccione un producto</option>
                    <!-- Se cargará dinámicamente con JavaScript -->
                </select>
                <small id="producto-loading" class="loading-text" style="display: none;">Cargando productos...</small>
                <small id="producto-error" class="error-text" style="display: none;">Error al cargar productos</small>
            </div>
            
            <div class="form-group">
                <label for="cantidad_disponible">Cantidad disponible:</label>
                <input type="number" id="cantidad_disponible" name="cantidad_disponible" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="fecha_lectura">Fecha de lectura:</label>
                <input type="datetime-local" id="fecha_lectura" name="fecha_lectura" value="<?= date('Y-m-d\TH:i') ?>" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary" <?= empty($botiquines) ? 'disabled' : '' ?>>Registrar lectura</button>
                <button type="reset" class="btn-secondary">Limpiar</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const botiquinSelect = document.getElementById('id_botiquin');
    const productoSelect = document.getElementById('id_producto');
    const productoLoading = document.getElementById('producto-loading');
    const productoError = document.getElementById('producto-error');
    const submitButton = document.querySelector('button[type="submit"]');
    
    botiquinSelect.addEventListener('change', function() {
        const botiquinId = this.value;
        productoSelect.disabled = true;
        productoSelect.innerHTML = '<option value="">Seleccione un producto</option>';
        
        if (!botiquinId) {
            return;
        }
        
        // Mostrar indicador de carga
        productoLoading.style.display = 'block';
        productoError.style.display = 'none';
        
        // Cargar productos asociados al botiquín seleccionado
        fetch(`/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/controller/routes/botiquin.routes.php?action=getProductos&id=${botiquinId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                productoLoading.style.display = 'none';
                
                if (data.error) {
                    productoError.textContent = data.mensaje || 'Error al cargar los productos';
                    productoError.style.display = 'block';
                    return;
                }
                
                if (data.productos && data.productos.length > 0) {
                    data.productos.forEach(producto => {
                        const option = document.createElement('option');
                        option.value = producto.id;
                        option.textContent = `${producto.codigo} - ${producto.nombre}`;
                        productoSelect.appendChild(option);
                    });
                    productoSelect.disabled = false;
                } else {
                    productoError.textContent = 'No hay productos asociados a este botiquín';
                    productoError.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error cargando productos:', error);
                productoLoading.style.display = 'none';
                productoError.textContent = 'Error al comunicarse con el servidor';
                productoError.style.display = 'block';
            });
    });
    
    // Validación del formulario antes de enviar
    const form = document.getElementById('formRegistrarLectura');
    form.addEventListener('submit', function(event) {
        const botiquinId = botiquinSelect.value;
        const productoId = productoSelect.value;
        const cantidad = document.getElementById('cantidad_disponible').value;
        const fecha = document.getElementById('fecha_lectura').value;
        
        let isValid = true;
        let errorMessage = '';
        
        if (!botiquinId) {
            errorMessage = 'Debe seleccionar un botiquín';
            isValid = false;
        } else if (!productoId) {
            errorMessage = 'Debe seleccionar un producto';
            isValid = false;
        } else if (!cantidad || cantidad < 0) {
            errorMessage = 'La cantidad debe ser un número positivo';
            isValid = false;
        } else if (!fecha) {
            errorMessage = 'Debe especificar una fecha y hora';
            isValid = false;
        }
        
        if (!isValid) {
            event.preventDefault();
            alert(errorMessage);
        }
    });
    
    // Limpieza de mensajes de error al hacer reset
    document.querySelector('button[type="reset"]').addEventListener('click', function() {
        productoSelect.disabled = true;
        productoSelect.innerHTML = '<option value="">Seleccione un producto</option>';
        productoError.style.display = 'none';
        productoLoading.style.display = 'none';
    });
    
    // Manejar alertas
    const alertCloseButtons = document.querySelectorAll('.list-alert__close');
    alertCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.parentElement.style.display = 'none';
        });
    });
});
</script>
