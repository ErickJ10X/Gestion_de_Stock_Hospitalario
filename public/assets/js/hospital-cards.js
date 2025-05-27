document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.querySelector('.hospital-overlay');
    const addHospitalButton = document.getElementById('btn-add-hospital');
    const createHospitalCard = document.getElementById('hospital-card-create');
    const addPlantaButton = document.getElementById('btn-add-planta');
    const createPlantaCard = document.getElementById('planta-card-create');
    const addBotiquinButton = document.getElementById('btn-add-botiquin');
    const createBotiquinCard = document.getElementById('botiquin-card-create');
    const editHospitalButtons = document.querySelectorAll('.btn-edit-hospital');
    const deleteHospitalButtons = document.querySelectorAll('.btn-delete-hospital');
    const editPlantaButtons = document.querySelectorAll('.btn-edit-planta');
    const deletePlantaButtons = document.querySelectorAll('.btn-delete-planta');
    const editBotiquinButtons = document.querySelectorAll('.btn-edit-botiquin');
    const deleteBotiquinButtons = document.querySelectorAll('.btn-delete-botiquin');
    const closeButtons = document.querySelectorAll('.hospital-card__close');
    const cancelButtons = document.querySelectorAll('.hospital-form__button--cancel');
    const alertCloseButtons = document.querySelectorAll('.list-alert__close');
    
    // Función para mostrar una tarjeta modal
    function showCard(card) {
        if (overlay && card) {
            overlay.style.display = 'block';
            card.style.display = 'block';
            document.body.style.overflow = 'hidden';
        } else {
            console.error('Overlay o tarjeta no encontrados');
        }
    }
    
    // Función para ocultar todas las tarjetas modales
    function hideCards() {
        if (overlay) {
            overlay.style.display = 'none';
        }
        
        document.querySelectorAll('.hospital-card').forEach(card => {
            card.style.display = 'none';
        });
        
        document.body.style.overflow = '';
    }
    
    // Inicializar ocultando todas las tarjetas
    hideCards();
    
    // Evento para botón de nuevo hospital
    if (addHospitalButton && createHospitalCard) {
        addHospitalButton.addEventListener('click', function(e) {
            e.preventDefault();
            showCard(createHospitalCard);
        });
    }
    
    // Evento para botón de nueva planta
    if (addPlantaButton && createPlantaCard) {
        addPlantaButton.addEventListener('click', function(e) {
            e.preventDefault();
            showCard(createPlantaCard);
        });
    }
    
    // Evento para botón de nuevo botiquín
    if (addBotiquinButton && createBotiquinCard) {
        addBotiquinButton.addEventListener('click', function(e) {
            e.preventDefault();
            showCard(createBotiquinCard);
        });
    }
    
    // Eventos para botones de editar hospital
    editHospitalButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const editCard = document.getElementById(`hospital-card-edit-${id}`);
            if (editCard) {
                showCard(editCard);
            } else {
                console.error(`Tarjeta hospital-card-edit-${id} no encontrada`);
            }
        });
    });
    
    // Eventos para botones de eliminar hospital
    deleteHospitalButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const deleteCard = document.getElementById(`hospital-card-delete-${id}`);
            if (deleteCard) {
                showCard(deleteCard);
            } else {
                console.error(`Tarjeta hospital-card-delete-${id} no encontrada`);
            }
        });
    });
    
    // Eventos para botones de editar planta
    editPlantaButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const editCard = document.getElementById(`planta-card-edit-${id}`);
            if (editCard) {
                showCard(editCard);
            } else {
                console.error(`Tarjeta planta-card-edit-${id} no encontrada`);
            }
        });
    });
    
    // Eventos para botones de eliminar planta
    deletePlantaButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const deleteCard = document.getElementById(`planta-card-delete-${id}`);
            if (deleteCard) {
                showCard(deleteCard);
            } else {
                console.error(`Tarjeta planta-card-delete-${id} no encontrada`);
            }
        });
    });
    
    // Eventos para botones de editar botiquín
    editBotiquinButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const editCard = document.getElementById(`botiquin-card-edit-${id}`);
            if (editCard) {
                showCard(editCard);
            } else {
                console.error(`Tarjeta botiquin-card-edit-${id} no encontrada`);
            }
        });
    });
    
    // Eventos para botones de eliminar botiquín
    deleteBotiquinButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const deleteCard = document.getElementById(`botiquin-card-delete-${id}`);
            if (deleteCard) {
                showCard(deleteCard);
            } else {
                console.error(`Tarjeta botiquin-card-delete-${id} no encontrada`);
            }
        });
    });
    
    // Eventos para botones de cerrar
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            hideCards();
        });
    });
    
    // Eventos para botones de cancelar
    cancelButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            hideCards();
        });
    });
    
    // Cerrar al hacer clic en el overlay
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                hideCards();
            }
        });
    }
    
    // Cerrar al presionar ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideCards();
        }
    });
    
    // Cerrar alertas manualmente
    alertCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const alert = this.closest('.list-alert');
            if (alert) {
                alert.style.animation = 'fadeOut 0.3s forwards';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 300);
            }
        });
    });
    
    // Auto-cerrar alertas después de 5 segundos
    setTimeout(function() {
        const alerts = document.querySelectorAll('.list-alert');
        alerts.forEach(function(alert) {
            alert.style.animation = 'fadeOut 0.3s forwards';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 300);
        });
    }, 5000);
    
    // Añadir efecto de fadeOut
    if (!document.querySelector('style#card-animations')) {
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
    
    // Exponer función para testing
    window.testShowModal = function(id, tipo) {
        const card = document.getElementById(`${tipo}-card-${id}`);
        if (card) {
            showCard(card);
            return true;
        } else {
            console.error(`Tarjeta ${tipo}-card-${id} no encontrada`);
            return false;
        }
    };
});
