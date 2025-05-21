<?php
include_once(__DIR__ . '/../util/session.php');
include_once(__DIR__ . '/../util/redirect.php');
include_once(__DIR__ . '/../util/authGuard.php');
require_once(__DIR__ . '/UsuarioController.php');

class AuthController {
    private Session $session;
    private UsuarioController $userController;
    private AuthGuard $authGuard;

    public function __construct(){
        $this->session = new Session();
        $this->userController = new UsuarioController();
        $this->authGuard = new AuthGuard();
    }

    public function login(): void {
        $this->authGuard->requireNoAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->sanitizar($_POST['email'] ?? '');
            $password = $_POST['contrasena'] ?? '';

            try {
                $user = $this->userController->login($email, $password);
                if ($user) {
                    Redirect::toHome();
                }
            } catch (Exception $e) {
                Redirect::withError('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/login.php', $e->getMessage());
            }
        }
    }

    public function logout(): void {
        $this->authGuard->requireAuth();

        $this->session->destroy();
        Redirect::toLogin();
    }

    public function register(): void{
        $this->authGuard->requireNoAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $this->sanitizar($_POST['nombre'] ?? '');
            $email = $this->sanitizar($_POST['email'] ?? '');
            $password = $_POST['contrasena'] ?? '';
            $confirmPassword = $_POST['confirmar_contrasena'] ?? '';
            $rol = $_POST['rol'] ?? 'Usuario de botiquín';

            if (!$this->confirmPassword($password, $confirmPassword)) {
                Redirect::withError('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/register.php', 'Las contraseñas no coinciden');
                return;
            }

            try {
                $this->userController->register($name, $email, $password, $rol);
                Redirect::withSuccess('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/login.php', 'Usuario registrado correctamente');
            } catch (Exception $e) {
                Redirect::withError('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/register.php', $e->getMessage());
            }
        }
    }

    public function sanitizar($input): string{
        $input = trim($input);
        $input = stripslashes($input);
        return htmlspecialchars($input);
    }

    public function confirmPassword($password, $confirmPassword): bool{
        return $password === $confirmPassword;
    }
}
