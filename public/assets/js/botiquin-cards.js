document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.querySelector('.botiquin-overlay');
    const addButton = document.getElementById('btn-add-botiquin');
    const createCard = document.getElementById('botiquin-card-create');
    const editButtons = document.querySelectorAll('.btn-edit-botiquin');
    const deleteButtons = document.querySelectorAll('.btn-delete-botiquin');
    const closeButtons = document.querySelectorAll('.botiquin-card__close');
    const cancelButtons = document.querySelectorAll('.botiquin-form__button--cancel');
    
    function showCard(card) {
        if (overlay) {
            overlay.classList.add('botiquin-overlay--active');
        }
        card.classList.add('botiquin-card--active');
        card.classList.add('botiquin-card--animate');
        document.body.style.overflow = 'hidden';
    }
    
    function hideCards() {
        const activeCards = document.querySelectorAll('.botiquin-card--active');
        if (overlay) {
            overlay.classList.remove('botiquin-overlay--active');
        }
        activeCards.forEach(card => {
            card.classList.remove('botiquin-card--active');
        });
        document.body.style.overflow = '';
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
            const botiquinId = this.dataset.id;
            const editCard = document.getElementById(`botiquin-card-edit-${botiquinId}`);
            if (editCard) {
                showCard(editCard);
            }
        });
    });
    
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
