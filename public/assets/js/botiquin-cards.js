/**
 * Script para manejar las tarjetas flotantes de botiquines
 */
document.addEventListener('DOMContentLoaded', function() {
    // Obtener referencias a elementos del DOM
    const overlay = document.querySelector('.botiquin-overlay');
    const addButton = document.getElementById('btn-add-botiquin');
    const createCard = document.getElementById('botiquin-card-create');
    const editButtons = document.querySelectorAll('.btn-edit-botiquin');
    const deleteButtons = document.querySelectorAll('.btn-delete-botiquin');
    const closeButtons = document.querySelectorAll('.botiquin-card__close');
    const cancelButtons = document.querySelectorAll('.botiquin-form__button--cancel');
    
    // Función para mostrar una tarjeta específica
    function showCard(card) {
        if (overlay) {
            overlay.classList.add('botiquin-overlay--active');
        }
        card.classList.add('botiquin-card--active');
        card.classList.add('botiquin-card--animate');
        document.body.style.overflow = 'hidden'; // Evita el desplazamiento de fondo
    }
    
    // Función para ocultar todas las tarjetas
    function hideCards() {
        const activeCards = document.querySelectorAll('.botiquin-card--active');
        if (overlay) {
            overlay.classList.remove('botiquin-overlay--active');
        }
        activeCards.forEach(card => {
            card.classList.remove('botiquin-card--active');
        });
        document.body.style.overflow = ''; // Restaura el desplazamiento
    }
    
    // Event listener para el botón de nuevo botiquín
    if (addButton) {
        addButton.addEventListener('click', function(e) {
            e.preventDefault();
            showCard(createCard);
        });
    }
    
    // Event listeners para los botones de editar
    editButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const botiquinId = this.dataset.id;
            const editCard = document.getElementById(`botiquin-card-edit-${botiquinId}`);
            if (editCard) {
                showCard(editCard);
            }
        });
    });
    
    // Event listeners para los botones de eliminar
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const botiquinId = this.dataset.id;
            const deleteCard = document.getElementById(`botiquin-card-delete-${botiquinId}`);
            if (deleteCard) {
                showCard(deleteCard);
            }
        });
    });
    
    // Event listeners para los botones de cerrar
    closeButtons.forEach(button => {
        button.addEventListener('click', hideCards);
    });
    
    // Event listeners para los botones de cancelar
    cancelButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            hideCards();
        });
    });
    
    // Cerrar la tarjeta al hacer clic en el overlay
    if (overlay) {
        overlay.addEventListener('click', hideCards);
    }
    
    // Cerrar la tarjeta al presionar ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideCards();
        }
    });
});
