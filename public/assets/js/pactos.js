/**
 * JavaScript específico para la gestión de pactos
 */

document.addEventListener('DOMContentLoaded', function() {
    // Cerrar alertas al hacer clic en el botón de cerrar
    const alertCloseButtons = document.querySelectorAll('.list-alert__close');
    alertCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const alert = this.parentElement;
            alert.style.display = 'none';
        });
    });

    // Inicializar cualquier otro comportamiento específico de pactos
    initializePactosForms();
});

/**
 * Inicializa comportamientos específicos de los formularios de pactos
 */
function initializePactosForms() {
    // Aquí puedes agregar comportamientos específicos adicionales para formularios de pactos
    // Por ejemplo, validaciones personalizadas, comportamientos de UI, etc.
    
    // Ejemplo: Validación de cantidad pactada positiva
    const cantidadInputs = document.querySelectorAll('input[name="cantidad_pactada"]');
    cantidadInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = parseInt(this.value);
            if (value <= 0) {
                this.setCustomValidity('La cantidad debe ser mayor que cero');
            } else {
                this.setCustomValidity('');
            }
        });
    });
}

/**
 * Carga los destinos en un select según el tipo de ubicación seleccionado
 * @param {HTMLSelectElement} tipoSelect - Select del tipo de ubicación
 * @param {HTMLSelectElement} destinoSelect - Select del destino a poblar
 * @param {Array} plantas - Array de plantas disponibles
 * @param {Array} botiquines - Array de botiquines disponibles
 * @param {Number|null} selectedId - ID del destino a seleccionar (opcional)
 */
function cargarDestinosPacto(tipoSelect, destinoSelect, plantas, botiquines, selectedId = null) {
    const tipoSeleccionado = tipoSelect.value;
    destinoSelect.innerHTML = '<option value="">Seleccione un destino</option>';
    
    if (tipoSeleccionado) {
        destinoSelect.disabled = false;
        let destinos = [];
        
        if (tipoSeleccionado === 'Planta') {
            destinos = plantas;
        } else if (tipoSeleccionado === 'Botiquin') {
            destinos = botiquines;
        }
        
        destinos.forEach(destino => {
            const option = document.createElement('option');
            option.value = destino.id;
            option.textContent = destino.nombre;
            
            if (selectedId && destino.id == selectedId) {
                option.selected = true;
            }
            
            destinoSelect.appendChild(option);
        });
    } else {
        destinoSelect.disabled = true;
    }
}
