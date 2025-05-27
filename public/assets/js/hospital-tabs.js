document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar todos los botones de pestaña
    const tabButtons = document.querySelectorAll('.tab-btn');
    // Seleccionar todos los paneles de contenido
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    // Función para activar una pestaña específica
    function activateTab(tabId) {
        // Desactivar todas las pestañas
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabPanes.forEach(pane => pane.classList.remove('active'));
        
        // Activar la pestaña seleccionada
        document.querySelector(`[data-tab="${tabId}"]`).classList.add('active');
        document.getElementById(tabId).classList.add('active');
        
        // Guardar la pestaña activa en sessionStorage
        sessionStorage.setItem('activeHospitalTab', tabId);
    }
    
    // Agregar listeners a todos los botones de pestaña
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            activateTab(tabId);
        });
    });
    
    // Verificar si hay una pestaña guardada en sessionStorage
    const savedTab = sessionStorage.getItem('activeHospitalTab');
    
    // Activar la pestaña guardada o la primera por defecto
    if (savedTab && document.getElementById(savedTab)) {
        activateTab(savedTab);
    } else {
        // Si no hay pestaña guardada, activar la primera
        const firstTabId = tabButtons[0].getAttribute('data-tab');
        activateTab(firstTabId);
    }
});
