document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.querySelector('.hospital-overlay');
    const addButton = document.getElementById('btn-add-hospital');
    const createCard = document.getElementById('hospital-card-create');
    const editButtons = document.querySelectorAll('.btn-edit-hospital');
    const deleteButtons = document.querySelectorAll('.btn-delete-hospital');
    const closeButtons = document.querySelectorAll('.hospital-card__close');
    const cancelButtons = document.querySelectorAll('.hospital-form__button--cancel');
    
    function showCard(card) {
        overlay.classList.add('hospital-overlay--active');
        card.classList.add('hospital-card--active');
        card.classList.add('hospital-card--animate');
        document.body.style.overflow = 'hidden';
    }
    
    function hideCards() {
        const activeCards = document.querySelectorAll('.hospital-card--active');
        overlay.classList.remove('hospital-overlay--active');
        activeCards.forEach(card => {
            card.classList.remove('hospital-card--active');
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
            const hospitalId = this.dataset.id;
            const editCard = document.getElementById(`hospital-card-edit-${hospitalId}`);
            if (editCard) {
                showCard(editCard);
            }
        });
    });
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const hospitalId = this.dataset.id;
            const deleteCard = document.getElementById(`hospital-card-delete-${hospitalId}`);
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
