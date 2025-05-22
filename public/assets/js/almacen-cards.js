/**
 * Script para manejar las tarjetas flotantes de almacenes
 */
document.addEventListener('DOMContentLoaded', function() {
    // Obtener referencias a elementos del DOM
    const overlay = document.querySelector('.almacen-overlay');
    const addButton = document.getElementById('btn-add-almacen');
    const createCard = document.getElementById('almacen-card-create');
    const editButtons = document.querySelectorAll('.btn-edit-almacen');
    const deleteButtons = document.querySelectorAll('.btn-delete-almacen');
    const closeButtons = document.querySelectorAll('.almacen-card__close');
    const cancelButtons = document.querySelectorAll('.almacen-form__button--cancel');
    
    // Función para mostrar una tarjeta específica
    function showCard(card) {
        overlay.classList.add('almacen-overlay--active');
        card.classList.add('almacen-card--active');
        card.classList.add('almacen-card--animate');
        document.body.style.overflow = 'hidden'; // Evita el desplazamiento de fondo
    }
    
    // Función para ocultar todas las tarjetas
    function hideCards() {
        const activeCards = document.querySelectorAll('.almacen-card--active');
        overlay.classList.remove('almacen-overlay--active');
        activeCards.forEach(card => {
            card.classList.remove('almacen-card--active');
        });
        document.body.style.overflow = ''; // Restaura el desplazamiento
    }
    
    // Event listener para el botón de nuevo almacén
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
            const almacenId = this.dataset.id;
            const editCard = document.getElementById(`almacen-card-edit-${almacenId}`);
            if (editCard) {
                showCard(editCard);
            }
        });
    });
    
    // Event listeners para los botones de eliminar
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const almacenId = this.dataset.id;
            const deleteCard = document.getElementById(`almacen-card-delete-${almacenId}`);
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
