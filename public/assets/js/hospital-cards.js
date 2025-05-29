document.addEventListener('DOMContentLoaded', function() {
    // Elementos DOM
    const overlay = document.querySelector('.hospital-overlay');
    const addHospitalButton = document.getElementById('btn-add-hospital');
    const addPlantaButton = document.getElementById('btn-add-planta');
    const addBotiquinButton = document.getElementById('btn-add-botiquin');
    const addAlmacenButton = document.getElementById('btn-add-almacen');
    const addProductoButton = document.getElementById('btn-add-producto');
    const addPactoButton = document.getElementById('btn-add-pacto');
    
    const createHospitalCard = document.getElementById('hospital-card-create');
    const createPlantaCard = document.getElementById('planta-card-create');
    const createBotiquinCard = document.getElementById('botiquin-card-create');
    const createAlmacenCard = document.getElementById('almacen-card-create');
    const createProductoCard = document.getElementById('producto-card-create');
    const createPactoCard = document.getElementById('pacto-card-create');

    // Log de estados iniciales para verificar
    console.log('Overlay encontrado:', overlay !== null);
    console.log('Botón agregar hospital encontrado:', addHospitalButton !== null);
    console.log('Botón agregar planta encontrado:', addPlantaButton !== null);
    console.log('Botón agregar botiquín encontrado:', addBotiquinButton !== null);
    console.log('Botón agregar almacén encontrado:', addAlmacenButton !== null);
    console.log('Botón agregar producto encontrado:', addProductoButton !== null);
    console.log('Botón agregar pacto encontrado:', addPactoButton !== null);
    console.log('Card crear hospital encontrada:', createHospitalCard !== null);
    console.log('Card crear planta encontrada:', createPlantaCard !== null);
    console.log('Card crear botiquín encontrada:', createBotiquinCard !== null);
    console.log('Card crear almacén encontrada:', createAlmacenCard !== null);
    console.log('Card crear producto encontrada:', createProductoCard !== null);
    console.log('Card crear pacto encontrada:', createPactoCard !== null);

    const editHospitalButtons = document.querySelectorAll('.btn-edit-hospital');
    console.log('Botones editar hospital encontrados:', editHospitalButtons.length);

    const deleteHospitalButtons = document.querySelectorAll('.btn-delete-hospital');
    console.log('Botones eliminar hospital encontrados:', deleteHospitalButtons.length);

    const editPlantaButtons = document.querySelectorAll('.btn-edit-planta');
    console.log('Botones editar planta encontrados:', editPlantaButtons.length);

    const deletePlantaButtons = document.querySelectorAll('.btn-delete-planta');
    console.log('Botones eliminar planta encontrados:', deletePlantaButtons.length);

    const editBotiquinButtons = document.querySelectorAll('.btn-edit-botiquin');
    console.log('Botones editar botiquín encontrados:', editBotiquinButtons.length);

    const deleteBotiquinButtons = document.querySelectorAll('.btn-delete-botiquin');
    console.log('Botones eliminar botiquín encontrados:', deleteBotiquinButtons.length);

    const editAlmacenButtons = document.querySelectorAll('.btn-edit-almacen');
    console.log('Botones editar almacén encontrados:', editAlmacenButtons.length);

    const deleteAlmacenButtons = document.querySelectorAll('.btn-delete-almacen');
    console.log('Botones eliminar almacén encontrados:', deleteAlmacenButtons.length);

    const editPactoButtons = document.querySelectorAll('.btn-edit-pacto');
    console.log('Botones editar pacto encontrados:', editPactoButtons.length);

    const deletePactoButtons = document.querySelectorAll('.btn-delete-pacto');
    console.log('Botones eliminar pacto encontrados:', deletePactoButtons.length);

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
        overlay.classList.add('hospital-overlay--active'); // Usamos la clase del CSS
        card.classList.add('hospital-card--active'); // Usamos la clase del CSS
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

    // Evento para el botón de agregar planta
    if (addPlantaButton) {
        addPlantaButton.addEventListener('click', function(e) {
            console.log('Click en botón agregar planta');
            e.preventDefault();
            showCard(createPlantaCard);
        });
    }

    // Evento para el botón de agregar botiquín
    if (addBotiquinButton) {
        addBotiquinButton.addEventListener('click', function(e) {
            console.log('Click en botón agregar botiquín');
            e.preventDefault();
            showCard(createBotiquinCard);
        });
    }

    // Evento para el botón de agregar almacén
    if (addAlmacenButton) {
        addAlmacenButton.addEventListener('click', function(e) {
            console.log('Click en botón agregar almacén');
            e.preventDefault();
            showCard(createAlmacenCard);
        });
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

    // Eventos para los botones de editar planta
    editPlantaButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Click en botón editar planta');
            e.preventDefault();
            const plantaId = this.getAttribute('data-id');
            const editCard = document.getElementById(`planta-card-edit-${plantaId}`);
            if (editCard) {
                showCard(editCard);
            }
        });
    });

    // Eventos para los botones de eliminar planta
    deletePlantaButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Click en botón eliminar planta');
            e.preventDefault();
            const plantaId = this.getAttribute('data-id');
            const deleteCard = document.getElementById(`planta-card-delete-${plantaId}`);
            if (deleteCard) {
                showCard(deleteCard);
            }
        });
    });

    // Eventos para los botones de editar botiquín
    editBotiquinButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Click en botón editar botiquín');
            e.preventDefault();
            const botiquinId = this.getAttribute('data-id');
            const editCard = document.getElementById(`botiquin-card-edit-${botiquinId}`);
            if (editCard) {
                showCard(editCard);
            } else {
                console.error(`No se encontró la tarjeta para editar botiquín con ID: ${botiquinId}`);
            }
        });
    });

    // Eventos para los botones de eliminar botiquín
    deleteBotiquinButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Click en botón eliminar botiquín');
            e.preventDefault();
            const botiquinId = this.getAttribute('data-id');
            const deleteCard = document.getElementById(`botiquin-card-delete-${botiquinId}`);
            if (deleteCard) {
                showCard(deleteCard);
            } else {
                console.error(`No se encontró la tarjeta para eliminar botiquín con ID: ${botiquinId}`);
            }
        });
    });

    // Eventos para los botones de editar almacén
    editAlmacenButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Click en botón editar almacén');
            e.preventDefault();
            const almacenId = this.getAttribute('data-id');
            const editCard = document.getElementById(`almacen-card-edit-${almacenId}`);
            if (editCard) {
                showCard(editCard);
            } else {
                console.error(`No se encontró la tarjeta para editar almacén con ID: ${almacenId}`);
            }
        });
    });

    // Eventos para los botones de eliminar almacén
    deleteAlmacenButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('Click en botón eliminar almacén');
            e.preventDefault();
            const almacenId = this.getAttribute('data-id');
            const deleteCard = document.getElementById(`almacen-card-delete-${almacenId}`);
            if (deleteCard) {
                showCard(deleteCard);
            } else {
                console.error(`No se encontró la tarjeta para eliminar almacén con ID: ${almacenId}`);
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

    console.log('Inicialización de hospital-cards.js completada');
});
