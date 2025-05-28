document.addEventListener('DOMContentLoaded', function() {
    console.log('tabs.js cargado correctamente');

    // Determinar el tipo de página (hospitales o usuarios) basado en la URL actual
    const isHospitalesPage = window.location.href.includes('hospitales');
    const isUsuariosPage = window.location.href.includes('usuarios');

    // Definir la clave de almacenamiento según la página
    const storageKey = isHospitalesPage ? 'activeHospitalTab' : 'activeUsuarioTab';

    console.log('Tipo de página detectado:', isHospitalesPage ? 'hospitales' : (isUsuariosPage ? 'usuarios' : 'desconocido'));
    console.log('Usando clave de storage:', storageKey);

    // Seleccionar todos los botones de pestaña
    const tabButtons = document.querySelectorAll('.tab-btn');
    // Seleccionar todos los paneles de contenido
    const tabPanes = document.querySelectorAll('.tab-pane');

    console.log('Botones de tabs encontrados:', tabButtons.length);
    console.log('Paneles de tabs encontrados:', tabPanes.length);

    // Función para activar una pestaña específica
    function activateTab(tabId) {
        console.log('Activando tab:', tabId);

        // Desactivar todas las pestañas
        tabButtons.forEach(btn => {
            btn.classList.remove('active');
            console.log('Desactivando botón:', btn.getAttribute('data-tab'));
        });

        tabPanes.forEach(pane => {
            pane.classList.remove('active');
            console.log('Desactivando panel:', pane.id);
        });

        // Activar la pestaña seleccionada
        const selectedButton = document.querySelector(`[data-tab="${tabId}"]`);
        const selectedPane = document.getElementById(tabId);

        if (selectedButton) {
            selectedButton.classList.add('active');
            console.log('Botón activado:', tabId);
        } else {
            console.error('No se encontró el botón con data-tab:', tabId);
        }

        if (selectedPane) {
            selectedPane.classList.add('active');
            console.log('Panel activado:', tabId);
        } else {
            console.error('No se encontró el panel con id:', tabId);
        }

        // Guardar la pestaña activa en sessionStorage
        sessionStorage.setItem(storageKey, tabId);
    }

    // Agregar listeners a todos los botones de pestaña
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const tabId = this.getAttribute('data-tab');
            console.log('Click en botón de tab:', tabId);
            activateTab(tabId);
        });
    });

    // Verificar si hay una pestaña guardada en sessionStorage
    const savedTab = sessionStorage.getItem(storageKey);
    console.log('Tab guardada en sessionStorage:', savedTab);

    // Activar la pestaña guardada o la primera por defecto
    if (savedTab && document.getElementById(savedTab)) {
        console.log('Restaurando tab guardada:', savedTab);
        activateTab(savedTab);
    } else {
        // Si no hay pestaña guardada, activar la primera
        if (tabButtons.length > 0) {
            const firstTabId = tabButtons[0].getAttribute('data-tab');
            console.log('Activando primera tab por defecto:', firstTabId);
            activateTab(firstTabId);
        } else {
            console.error('No se encontraron botones de tab');
        }
    }
});
