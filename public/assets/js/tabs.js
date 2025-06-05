document.addEventListener('DOMContentLoaded', function() {
    // Sistema de pestañas
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    // Verificar si hay pestañas en la página
    if (tabBtns.length && tabPanes.length) {
        // Activar la pestaña inicial si ninguna tiene la clase active
        let activeTabExists = false;
        tabBtns.forEach(btn => {
            if (btn.classList.contains('active')) {
                activeTabExists = true;
                const tabId = btn.getAttribute('data-tab');
                if (document.getElementById(tabId)) {
                    document.getElementById(tabId).classList.add('active');
                }
            }
        });

        // Si ninguna tiene active, activamos la primera
        if (!activeTabExists && tabBtns.length > 0) {
            tabBtns[0].classList.add('active');
            const firstTabId = tabBtns[0].getAttribute('data-tab');
            if (document.getElementById(firstTabId)) {
                document.getElementById(firstTabId).classList.add('active');
            }
        }

        // Añadir evento click a cada botón de pestaña
        tabBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                // Remover clase active de todos los botones y paneles
                tabBtns.forEach(b => b.classList.remove('active'));
                tabPanes.forEach(p => p.classList.remove('active'));
                
                // Agregar clase active al botón clickeado
                this.classList.add('active');
                
                // Mostrar el panel correspondiente
                const tabId = this.getAttribute('data-tab');
                if (document.getElementById(tabId)) {
                    document.getElementById(tabId).classList.add('active');
                }
            });
        });
    }
});
