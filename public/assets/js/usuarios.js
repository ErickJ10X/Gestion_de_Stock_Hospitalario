// Cambia la pestaña activa y muestra el panel correspondiente
function activarPestania(tabId) {
    // Quitar clase active de todos los botones y paneles
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));

    // Activar el botón y el panel correspondiente
    const btn = document.querySelector(`.tab-btn[data-tab="${tabId}"]`);
    const pane = document.getElementById(tabId);
    if (btn) btn.classList.add('active');
    if (pane) pane.classList.add('active');
}

// Mostrar formulario de edición y seleccionar usuario
function showEditUserTab(userId) {
    activarPestania('tab-crear-editar');
    setTimeout(() => {
        // Activar botón "Editar Usuario" y desactivar "Crear Usuario"
        const btnEditar = document.querySelector('.form-toggle-btn[data-target="editarForm"]');
        const btnCrear = document.querySelector('.form-toggle-btn[data-target="crearForm"]');
        if (btnEditar) btnEditar.classList.add('active');
        if (btnCrear) btnCrear.classList.remove('active');
        // Mostrar sección editar y ocultar crear
        const editarForm = document.getElementById('editarForm');
        const crearForm = document.getElementById('crearForm');
        if (editarForm) editarForm.style.display = '';
        if (crearForm) crearForm.style.display = 'none';

        // Seleccionar usuario en el selector
        const select = document.getElementById('seleccionarUsuario');
        if (select) {
            select.value = userId;
            select.dispatchEvent(new Event('change'));
        }
        // Ocultar mensaje y mostrar formulario de edición
        const mensaje = document.getElementById('mensajeSeleccion');
        if (mensaje) mensaje.style.display = 'none';
        const formEditar = document.getElementById('formEditarContainer');
        if (formEditar) formEditar.style.display = '';
    }, 100);
}

// Cambiar a la pestaña de ubicaciones y seleccionar usuario
function showUserLocationsTab(userId) {
    activarPestania('tab-ubicaciones');
    setTimeout(() => {
        const select = document.getElementById('verUsuarioUbicaciones');
        if (select) {
            select.value = userId;
            select.dispatchEvent(new Event('change'));
        }
        // Mostrar la sección de ver ubicaciones si existe
        const verUbicacionesForm = document.getElementById('verUbicacionesForm');
        if (verUbicacionesForm) {
            // Activar el botón de ver ubicaciones si existe
            const btns = document.querySelectorAll('#tab-ubicaciones .form-toggle-btn');
            btns.forEach(btn => btn.classList.remove('active'));
            const btnVer = document.querySelector('#tab-ubicaciones .form-toggle-btn[data-target="verUbicacionesForm"]');
            if (btnVer) btnVer.classList.add('active');
            // Mostrar solo el panel de ver ubicaciones
            document.querySelectorAll('#tab-ubicaciones .form-section').forEach(sec => sec.style.display = 'none');
            verUbicacionesForm.style.display = '';
        }
    }, 100);
}

// Mostrar formulario de creación de usuario
function showCreateUserForm() {
    activarPestania('tab-crear-editar');
    setTimeout(() => {
        // Activar botón "Crear Usuario" y desactivar "Editar Usuario"
        const btnCrear = document.querySelector('.form-toggle-btn[data-target="crearForm"]');
        const btnEditar = document.querySelector('.form-toggle-btn[data-target="editarForm"]');
        if (btnCrear) btnCrear.classList.add('active');
        if (btnEditar) btnEditar.classList.remove('active');
        // Mostrar sección crear y ocultar editar
        const crearForm = document.getElementById('crearForm');
        const editarForm = document.getElementById('editarForm');
        if (crearForm) crearForm.style.display = '';
        if (editarForm) editarForm.style.display = 'none';
        // Limpiar selección de usuario
        const select = document.getElementById('seleccionarUsuario');
        if (select) select.value = '';
        const mensaje = document.getElementById('mensajeSeleccion');
        if (mensaje) mensaje.style.display = '';
        const formEditar = document.getElementById('formEditarContainer');
        if (formEditar) formEditar.style.display = 'none';
    }, 100);
}

// Delegar eventos en botones de la tabla de usuarios
document.addEventListener('DOMContentLoaded', function () {
    // Botón editar
    document.querySelectorAll('.list-table__button--edit').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const userId = this.closest('tr').querySelector('td').textContent.trim();
            showEditUserTab(userId);
        });
    });

    // Botón ubicaciones
    document.querySelectorAll('.list-table__button--locations').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const userId = this.closest('tr').querySelector('td').textContent.trim();
            showUserLocationsTab(userId);
        });
    });

    // Botón nuevo usuario
    const btnNuevo = document.getElementById('btnNuevoUsuario');
    if (btnNuevo) {
        btnNuevo.addEventListener('click', function (e) {
            e.preventDefault();
            showCreateUserForm();
        });
    }

    // Alternar entre formularios de crear y editar usuario en la pestaña Agregar/Editar
    const formToggleBtns = document.querySelectorAll('.form-toggle-btn');
    const formSections = document.querySelectorAll('.form-section');
    formToggleBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            // Quitar clase active de todos los botones
            formToggleBtns.forEach(b => b.classList.remove('active'));
            // Agregar clase active al botón clickeado
            this.classList.add('active');
            // Mostrar la sección correspondiente y ocultar las demás
            const target = this.getAttribute('data-target');
            formSections.forEach(section => {
                if (section.id === target) {
                    section.style.display = '';
                } else {
                    section.style.display = 'none';
                }
            });
        });
    });
});