document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.querySelector('.planta-overlay');
    const addButton = document.getElementById('btn-add-planta');
    const createCard = document.getElementById('planta-card-create');
    const editButtons = document.querySelectorAll('.btn-edit-planta');
    const deleteButtons = document.querySelectorAll('.btn-delete-planta');
    const closeButtons = document.querySelectorAll('.planta-card__close');
    const cancelButtons = document.querySelectorAll('.planta-form__button--cancel');
    
    function showCard(card) {
        overlay.classList.add('planta-overlay--active');
        card.classList.add('planta-card--active');
        card.classList.add('planta-card--animate');
        document.body.style.overflow = 'hidden';
    }
    
    function hideCards() {
        const activeCards = document.querySelectorAll('.planta-card--active');
        overlay.classList.remove('planta-overlay--active');
        activeCards.forEach(card => {
            card.classList.remove('planta-card--active');
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
            const plantaId = this.dataset.id;
            const editCard = document.getElementById(`planta-card-edit-${plantaId}`);
            if (editCard) {
                showCard(editCard);
            }
        });
    });
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const plantaId = this.dataset.id;
            const deleteCard = document.getElementById(`planta-card-delete-${plantaId}`);
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
