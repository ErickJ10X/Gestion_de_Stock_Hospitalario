<?php
include_once(__DIR__ . '/session.php');
include_once(__DIR__ . '/redirect.php');

class AuthGuard {
    private Session $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function requireAuth(): void {
        if (!$this->session->isAuthenticated()) {
            Redirect::withError('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/login.php', 'Debes iniciar sesión para acceder a esta página');
        }
    }

    public function requireNoAuth(): void {
        if ($this->session->isAuthenticated()) {
            Redirect::toHome();
        }
    }

    public function requireAdmin(): void {
        $this->requireAuth();

        if (!$this->session->isAdmin()) {
            Redirect::withError('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/index.php', 'No tienes permisos para acceder a esta página');
        }
    }
}
