document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.querySelector('.almacen-overlay');
    const addButton = document.getElementById('btn-add-almacen');
    const createCard = document.getElementById('almacen-card-create');
    const editButtons = document.querySelectorAll('.btn-edit-almacen');
    const deleteButtons = document.querySelectorAll('.btn-delete-almacen');
    const closeButtons = document.querySelectorAll('.almacen-card__close');
    const cancelButtons = document.querySelectorAll('.almacen-form__button--cancel');
    
    function showCard(card) {
        overlay.classList.add('almacen-overlay--active');
        card.classList.add('almacen-card--active');
        card.classList.add('almacen-card--animate');
        document.body.style.overflow = 'hidden';
    }
    
    function hideCards() {
        const activeCards = document.querySelectorAll('.almacen-card--active');
        overlay.classList.remove('almacen-overlay--active');
        activeCards.forEach(card => {
            card.classList.remove('almacen-card--active');
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
            const almacenId = this.dataset.id;
            const editCard = document.getElementById(`almacen-card-edit-${almacenId}`);
            if (editCard) {
                showCard(editCard);
            }
        });
    });
    
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
