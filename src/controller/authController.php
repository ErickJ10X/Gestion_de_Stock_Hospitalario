<?php
include_once(__DIR__ . '/../util/session.php');
include_once(__DIR__ . '/../util/redirect.php');
include_once(__DIR__ . '/../util/authGuard.php');
require_once(__DIR__ . '/userController.php');

class AuthController {
    private Session $session;
    private UserController $userController;
    private AuthGuard $authGuard;

    public function __construct(){
        $this->session = new Session();
        $this->userController = new UserController();
        $this->authGuard = new AuthGuard();
    }

    public function login(): void {
        $this->authGuard->requireNoAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $this->sanitizar($_POST['usuario'] ?? '');
            $password = $_POST['contrasena'] ?? '';

            try {
                $user = $this->userController->login($username, $password);
                if ($user) {
                    Redirect::toHome();
                }
            } catch (Exception $e) {
                Redirect::withError('/gestorUsuarios/src/view/auth/login.php', $e->getMessage());
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

            if (!$this->confirmPassword($password, $confirmPassword)) {
                Redirect::withError('/gestorUsuarios/src/view/auth/register.php', 'Las contraseñas no coinciden');
                return;
            }

            try {
                $this->userController->register($name, $email, $password);
                Redirect::withSuccess('/gestorUsuarios/src/view/auth/login.php', 'registered');
            } catch (Exception $e) {
                Redirect::withError('/gestorUsuarios/src/view/auth/register.php', $e->getMessage());
            }
        }
    }

    public function updateProfile(): void{
        $this->authGuard->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $this->sanitizar($_POST['nombre'] ?? '');
            $surname = $this->sanitizar($_POST['apellido'] ?? '');
            $username = $this->sanitizar($_POST['usuario'] ?? '');
            $email = $this->sanitizar($_POST['email'] ?? '');
            $password = $_POST['contrasena'] ?? '';

            try {
                $this->userController->updateProfile($name, $surname, $username, $email, $password);
                Redirect::withSuccess('/gestorUsuarios/src/view/user/profile.php', 'updated');
            } catch (Exception $e) {
                Redirect::withError('/gestorUsuarios/src/view/user/edit_profile.php', $e->getMessage());
            }
        }
    }

    public function viewProfile(): void{
        $this->authGuard->requireAuth();
    }

    public function viewAdminDashboard(): PDOStatement|false
    {
        $this->authGuard->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
            $username = $_POST['username'] ?? '';

            try {
                $this->userController->deleteUser($username);
                Redirect::withSuccess('/gestorUsuarios/src/view/admin/dashboard.php', 'deleted');
            } catch (Exception $e) {
                Redirect::withError('/gestorUsuarios/src/view/admin/dashboard.php', $e->getMessage());
            }
        }

        return $this->userController->getAllUsers();
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