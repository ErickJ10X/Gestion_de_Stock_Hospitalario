document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.querySelector('.producto-overlay');
    const addButton = document.getElementById('btn-add-producto');
    const createCard = document.getElementById('producto-card-create');
    const editButtons = document.querySelectorAll('.btn-edit-producto');
    const deleteButtons = document.querySelectorAll('.btn-delete-producto');
    const closeButtons = document.querySelectorAll('.producto-card__close');
    const cancelButtons = document.querySelectorAll('.producto-form__button--cancel');
    
    function showCard(card) {
        overlay.classList.add('producto-overlay--active');
        card.classList.add('producto-card--active');
        card.classList.add('producto-card--animate');
        document.body.style.overflow = 'hidden';
    }
    
    function hideCards() {
        const activeCards = document.querySelectorAll('.producto-card--active');
        overlay.classList.remove('producto-overlay--active');
        activeCards.forEach(card => {
            card.classList.remove('producto-card--active');
        });
        document.body.style.overflow = ''; // Restaura el desplazamiento
    }
    
    if (addButton) {
        addButton.addEventListener('click', function(e) {
            e.preventDefault();
            showCard(createCard);
        });
    }
    
    editButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productoId = this.dataset.id;
            const editCard = document.getElementById(`producto-card-edit-${productoId}`);
            if (editCard) {
                showCard(editCard);
            }
        });
    });
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productoId = this.dataset.id;
            const deleteCard = document.getElementById(`producto-card-delete-${productoId}`);
            if (deleteCard) {
                showCard(deleteCard);
            }
        });
    });
    
    closeButtons.forEach(button => {
        button.addEventListener('click', hideCards);
    });
    
    cancelButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            hideCards();
        });
    });
    
    if (overlay) {
        overlay.addEventListener('click', hideCards);
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideCards();
        }
    });
});
