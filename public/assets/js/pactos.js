document.addEventListener('DOMContentLoaded', function() {
    // Cerrar alertas
    const alertCloseButtons = document.querySelectorAll('.list-alert__close');
    alertCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const alert = this.closest('.list-alert');
            alert.style.display = 'none';
        });
    });

    // Cerrar modal al hacer clic fuera
    const overlay = document.querySelector('.hospital-overlay');
    if (overlay) {
        overlay.addEventListener('click', function() {
            const cards = document.querySelectorAll('.hospital-card');
            cards.forEach(card => {
                card.classList.remove('hospital-card--visible');
            });
            this.classList.remove('hospital-overlay--visible');
        });
    }
});

/**
 * Muestra un mensaje de alerta
 * @param {string} tipo - Tipo de alerta (success, error)
 * @param {string} mensaje - Mensaje a mostrar
 */
function mostrarAlerta(tipo, mensaje) {
    const alertClass = tipo === 'success' ? 'list-alert--success' : 'list-alert--error';
    const container = document.querySelector('.list-container');
    
    // Crear alerta
    const alertDiv = document.createElement('div');
    alertDiv.className = `list-alert ${alertClass}`;
    
    // Crear mensaje
    const messageP = document.createElement('p');
    messageP.className = 'list-alert__message';
    messageP.textContent = mensaje;
    alertDiv.appendChild(messageP);
    
    // Crear botón de cierre
    const closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.className = 'list-alert__close';
    closeBtn.innerHTML = '&times;';
    closeBtn.addEventListener('click', function() {
        alertDiv.style.display = 'none';
    });
    alertDiv.appendChild(closeBtn);
    
    // Insertar alerta al inicio del contenedor
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto eliminar después de 5 segundos
    setTimeout(function() {
        alertDiv.style.display = 'none';
    }, 5000);
}
