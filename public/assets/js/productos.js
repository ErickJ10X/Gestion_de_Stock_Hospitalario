document.addEventListener('DOMContentLoaded', function() {
    // Elementos DOM relacionados con productos
    const addProductoButton = document.getElementById('btn-add-producto');
    const addPactoButton = document.getElementById('btn-add-pacto');

    const createProductoCard = document.getElementById('producto-card-create');
    const createPactoCard = document.getElementById('pacto-card-create');

    const editProductoButtons = document.querySelectorAll('.btn-edit-producto');
    console.log('Botones editar producto encontrados:', editProductoButtons.length);

    const deleteProductoButtons = document.querySelectorAll('.btn-delete-producto');
    console.log('Botones eliminar producto encontrados:', deleteProductoButtons.length);

    const editPactoButtons = document.querySelectorAll('.btn-edit-pacto');
    console.log('Botones editar pacto encontrados:', editPactoButtons.length);

    const deletePactoButtons = document.querySelectorAll('.btn-delete-pacto');
    console.log('Botones eliminar pacto encontrados:', deletePactoButtons.length);

    // Referencia al overlay (podría estar compartido con otros módulos)
    const overlay = document.querySelector('.hospital-overlay');

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

    // Evento para el botón de agregar producto
    if (addProductoButton) {
        addProductoButton.addEventListener('click', function(e) {
            console.log('Click en botón agregar producto');
            e.preventDefault();
            showCard(createProductoCard);
        });
    }

    // Evento para el botón de agregar pacto
    if (addPactoButton) {
        addPactoButton.addEventListener('click', function(e) {
            console.log('Click en botón agregar pacto');
            e.preventDefault();
            showCard(createPactoCard);
        });
    }

    // Eventos para los botones de editar producto
    editProductoButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Click en botón editar producto');
            e.preventDefault();
            const productoId = this.getAttribute('data-id');
            const editCard = document.getElementById(`producto-card-edit-${productoId}`);
            if (editCard) {
                showCard(editCard);
            } else {
                console.error(`No se encontró la tarjeta para editar producto con ID: ${productoId}`);
            }
        });
    });

    // Eventos para los botones de eliminar producto
    deleteProductoButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Click en botón eliminar producto');
            e.preventDefault();
            const productoId = this.getAttribute('data-id');
            const deleteCard = document.getElementById(`producto-card-delete-${productoId}`);
            if (deleteCard) {
                showCard(deleteCard);
            } else {
                console.error(`No se encontró la tarjeta para eliminar producto con ID: ${productoId}`);
            }
        });
    });

    // Eventos para los botones de editar pacto
    editPactoButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Click en botón editar pacto');
            e.preventDefault();
            const pactoId = this.getAttribute('data-id');
            const editCard = document.getElementById(`pacto-card-edit-${pactoId}`);
            if (editCard) {
                showCard(editCard);
            } else {
                console.error(`No se encontró la tarjeta para editar pacto con ID: ${pactoId}`);
            }
        });
    });

    // Eventos para los botones de eliminar pacto
    deletePactoButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Click en botón eliminar pacto');
            e.preventDefault();
            const pactoId = this.getAttribute('data-id');
            const deleteCard = document.getElementById(`pacto-card-delete-${pactoId}`);
            if (deleteCard) {
                showCard(deleteCard);
            } else {
                console.error(`No se encontró la tarjeta para eliminar pacto con ID: ${pactoId}`);
            }
        });
    });

    // Eventos para los botones de cerrar
    const closeButtons = document.querySelectorAll('.hospital-card__close');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('Click en botón cerrar');
            hideCards();
        });
    });

    // Eventos para los botones de cancelar
    const cancelButtons = document.querySelectorAll('.hospital-form__button--cancel');
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

    console.log('Inicialización de productos.js completada');
});
