</main>
<footer class="footer">
    <div class="container footer__container">
        <div class="footer__info">
            <p class="footer__copyright">&copy; <?php echo date('Y'); ?> Pegasus Medical - Gestión Hospitalaria</p>
            <?php if (isset($_SESSION['id'])): ?>
                <small class="footer__user-info">
                    <i class="bi bi-person-circle"></i>
                    Conectado como: <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong>
                    (<?php echo htmlspecialchars($_SESSION['rol']); ?>)
                </small>
            <?php endif; ?>
        </div>

        <ul class="footer__links">
            <li><a href="/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public" class="footer__link">Inicio</a></li>
            <li><a href="#" class="footer__link">Ayuda</a></li>
            <li><a href="#" class="footer__link">Privacidad</a></li>
        </ul>

        <div class="footer__social">
            <a href="#" class="footer__social-link" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
            <a href="#" class="footer__social-link" title="Twitter"><i class="bi bi-twitter-x"></i></a>
            <a href="#" class="footer__social-link" title="GitHub"><i class="bi bi-github"></i></a>
        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navToggle = document.getElementById('navToggle');
        const mainNav = document.getElementById('mainNav');

        if (navToggle) {
            navToggle.addEventListener('click', function() {
                mainNav.classList.toggle('nav--active');
            });
        }

        const closeButtons = document.querySelectorAll('.alert__close');
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const alert = this.parentElement;
                alert.style.display = 'none';
            });
        });
    });
</script>
</body>
</html>