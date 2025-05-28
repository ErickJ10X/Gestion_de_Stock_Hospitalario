document.addEventListener('DOMContentLoaded', function() {
    console.log('hospital-cards.js cargado correctamente');

    // Elementos DOM
    const overlay = document.querySelector('.hospital-overlay');
    const addHospitalButton = document.getElementById('btn-add-hospital');
    const createHospitalCard = document.getElementById('hospital-card-create');
    
    // Log de estados iniciales para verificar
    console.log('Overlay encontrado:', overlay !== null);
    console.log('Botón agregar hospital encontrado:', addHospitalButton !== null);
    console.log('Card crear hospital encontrada:', createHospitalCard !== null);
    
    const editHospitalButtons = document.querySelectorAll('.btn-edit-hospital');
    console.log('Botones editar hospital encontrados:', editHospitalButtons.length);
    
    const deleteHospitalButtons = document.querySelectorAll('.btn-delete-hospital');
    console.log('Botones eliminar hospital encontrados:', deleteHospitalButtons.length);
    
    const closeButtons = document.querySelectorAll('.hospital-card__close');
    console.log('Botones cerrar encontrados:', closeButtons.length);
    
    const cancelButtons = document.querySelectorAll('.hospital-form__button--cancel');
    console.log('Botones cancelar encontrados:', cancelButtons.length);
    
    const alertCloseButtons = document.querySelectorAll('.list-alert__close');
    console.log('Botones cerrar alerta encontrados:', alertCloseButtons.length);
    
    // Función para mostrar una tarjeta modal con animación
    function showCard(card) {
        if (!overlay || !card) {
            console.error('No se puede mostrar la tarjeta - overlay o card no disponible');
            return;
        }
        
        console.log('Mostrando card:', card.id);
        overlay.classList.add('hospital-overlay--active');
        card.classList.add('hospital-card--active');
        card.classList.add('hospital-card--animate');
        document.body.style.overflow = 'hidden';
    }
    
    // Función para ocultar todas las tarjetas modales
    function hideCards() {
        console.log('Ocultando todas las cards');
        
        if (!overlay) {
            console.error('No se pueden ocultar las tarjetas - overlay no disponible');
            return;
        }
        
        const activeCards = document.querySelectorAll('.hospital-card--active');
        console.log('Cards activas encontradas:', activeCards.length);
        
        overlay.classList.remove('hospital-overlay--active');
        activeCards.forEach(card => {
            card.classList.remove('hospital-card--active');
        });
        document.body.style.overflow = '';
    }
    
    // Evento para el botón de agregar hospital
    if (addHospitalButton) {
        addHospitalButton.addEventListener('click', function(e) {
            console.log('Click en botón agregar hospital');
            e.preventDefault();
            showCard(createHospitalCard);
        });
    }
    
    // Eventos para los botones de editar hospital
    editHospitalButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Click en botón editar hospital');
            e.preventDefault();
            const hospitalId = this.getAttribute('data-id');
            const editCard = document.getElementById(`hospital-card-edit-${hospitalId}`);
            if (editCard) {
                showCard(editCard);
            }
        });
    });
    
    // Eventos para los botones de eliminar hospital
    deleteHospitalButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Click en botón eliminar hospital');
            e.preventDefault();
            const hospitalId = this.getAttribute('data-id');
            const deleteCard = document.getElementById(`hospital-card-delete-${hospitalId}`);
            if (deleteCard) {
                showCard(deleteCard);
            }
        });
    });
    
    // Eventos para los botones de cerrar
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('Click en botón cerrar');
            hideCards();
        });
    });
    
    // Eventos para los botones de cancelar
    cancelButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Click en botón cancelar');
            e.preventDefault();
            hideCards();
        });
    });
    
    // Cerrar al hacer clic en el overlay
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                console.log('Click en overlay');
                hideCards();
            }
        });
    }
    
    // Cerrar al presionar ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            console.log('Tecla ESC presionada');
            hideCards();
        }
    });
    
    // Añadir efecto de fadeOut
    if (!document.querySelector('style#card-animations')) {
        console.log('Añadiendo estilos de animación');
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
    
    console.log('Inicialización de hospital-cards.js completada');
});
