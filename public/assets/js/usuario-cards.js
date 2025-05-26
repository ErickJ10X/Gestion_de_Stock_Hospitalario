document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.querySelector('.usuario-overlay');
    const addButton = document.getElementById('btn-add-usuario');
    const createCard = document.getElementById('usuario-card-create');
    const editButtons = document.querySelectorAll('.btn-edit-usuario');
    const deleteButtons = document.querySelectorAll('.btn-delete-usuario');
    const viewUbicacionesButtons = document.querySelectorAll('.btn-view-ubicaciones');
    const asignUbicacionButtons = document.querySelectorAll('.btn-asign-ubicacion');
    const closeButtons = document.querySelectorAll('.usuario-card__close');
    const cancelButtons = document.querySelectorAll('.usuario-form__button--cancel');
    const alertCloseButtons = document.querySelectorAll('.list-alert__close');
    
    // Función para mostrar una tarjeta modal con animación
    function showCard(card) {
        overlay.classList.add('usuario-overlay--active');
        card.classList.add('usuario-card--active');
        card.classList.add('usuario-card--animate');
        document.body.style.overflow = 'hidden';
    }
    
    // Función para ocultar todas las tarjetas modales
    function hideCards() {
        const activeCards = document.querySelectorAll('.usuario-card--active');
        overlay.classList.remove('usuario-overlay--active');
        activeCards.forEach(card => {
            card.classList.remove('usuario-card--active');
        });
        document.body.style.overflow = '';
    }
    
    // Evento para el botón de agregar usuario
    if (addButton) {
        addButton.addEventListener('click', function(e) {
            e.preventDefault();
            showCard(createCard);
        });
    }
    
    // Eventos para los botones de editar usuario
    editButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const usuarioId = this.getAttribute('data-id');
            const editCard = document.getElementById(`usuario-card-edit-${usuarioId}`);
            if (editCard) {
                showCard(editCard);
            }
        });
    });
    
    // Eventos para los botones de eliminar usuario
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const usuarioId = this.getAttribute('data-id');
            const deleteCard = document.getElementById(`usuario-card-delete-${usuarioId}`);
            if (deleteCard) {
                showCard(deleteCard);
            }
        });
    });
    
    // Eventos para los botones de ver ubicaciones
    viewUbicacionesButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const usuarioId = this.getAttribute('data-id');
            const ubicacionesCard = document.getElementById(`ubicaciones-card-${usuarioId}`);
            if (ubicacionesCard) {
                showCard(ubicacionesCard);
            }
        });
    });
    
    // Eventos para los botones de asignar ubicaciones
    asignUbicacionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const usuarioId = this.getAttribute('data-id');
            window.location.href = `/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/usuarios/asignar-ubicaciones.php?id=${usuarioId}`;
        });
    });
    
    // Eventos para los botones de cerrar
    closeButtons.forEach(button => {
        button.addEventListener('click', hideCards);
    });
    
    // Eventos para los botones de cancelar
    cancelButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            hideCards();
        });
    });
    
    // Cerrar al hacer clic en el overlay (solo si se hace clic directamente sobre él)
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                hideCards();
            }
        });
    }
    
    // Cerrar al presionar ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideCards();
        }
    });
    
    // Cerrar alertas manualmente
    alertCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const alert = this.closest('.list-alert');
            if (alert) {
                alert.style.animation = 'fadeOut 0.3s forwards';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 300);
            }
        });
    });
    
    // Auto-cerrar alertas después de 5 segundos
    setTimeout(function() {
        const alerts = document.querySelectorAll('.list-alert');
        alerts.forEach(function(alert) {
            alert.style.animation = 'fadeOut 0.3s forwards';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 300);
        });
    }, 5000);
    
    // Añadir efecto de fadeOut
    if (!document.querySelector('style#card-animations')) {
        const styleEl = document.createElement('style');
        styleEl.id = 'card-animations';
        styleEl.innerHTML = `
            @keyframes fadeOut {
                from { opacity: 1; }
                to { opacity: 0; }
            }
        `;
        document.head.appendChild(styleEl);
    }
});
