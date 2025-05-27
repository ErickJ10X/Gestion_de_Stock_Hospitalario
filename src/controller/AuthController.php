<?php

namespace controller;

use util\Redirect;
use util\Session;
use util\AuthGuard;
use Exception;
use model\enum\RolEnum;

include_once(__DIR__ . '/../util/Session.php');
include_once(__DIR__ . '/../util/Redirect.php');
include_once(__DIR__ . '/../util/AuthGuard.php');
require_once(__DIR__ . '/UsuarioController.php');
require_once(__DIR__ . '/../model/enum/RolEnum.php');

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
                    if (isset($user['ubicaciones']) && !empty($user['ubicaciones'])) {
                        Redirect::toHome();
                    } else {
                        $rolRequiereUbicacion = in_array($user['rol'], [
                            RolEnum::USUARIO_BOTIQUIN,
                            RolEnum::GESTOR_PLANTA
                        ]);

                        if ($rolRequiereUbicacion) {
                            Redirect::withWarning('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/user/profile.php', 'Tu cuenta no tiene ubicaciones asignadas. Contacta con un dashboard.');
                        } else {
                            Redirect::toHome();
                        }
                    }
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

    public function register(): void {
        $this->authGuard->requireNoAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $this->sanitizar($_POST['nombre'] ?? '');
            $email = $this->sanitizar($_POST['email'] ?? '');
            $password = $_POST['contrasena'] ?? '';
            $confirmPassword = $_POST['confirmar_contrasena'] ?? '';

            if (!$this->confirmPassword($password, $confirmPassword)) {
                $_SESSION['error_message'] = 'Las contraseñas no coinciden';
                Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/register.php');
                return;
            }

            try {
                $this->userController->register($name, $email, $password);

                if ($this->session->isLoggedIn() && $this->session->getUserData('rol') == RolEnum::ADMINISTRADOR) {
                    Redirect::withSuccess('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/admin/usuarios.php', 'Usuario registrado correctamente. Recuerda asignarle ubicaciones si es necesario.');
                } else {
                    Redirect::withSuccess('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/login.php', 'Usuario registrado correctamente');
                }
            } catch (Exception $e) {
                $_SESSION['error_message'] = $e->getMessage();
                Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/register.php');
            }
        }
    }

    public function updateUserProfile(): void {
        $this->authGuard->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? $this->session->getUserData('id');
            $name = $this->sanitizar($_POST['nombre'] ?? '');
            $email = $this->sanitizar($_POST['email'] ?? '');
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmNewPassword = $_POST['confirm_new_password'] ?? '';

            try {
                if (!empty($newPassword)) {
                    $user = $this->userController->getUserById($id);
                    if (!$user->verificarContrasena($currentPassword)) {
                        throw new Exception("La contraseña actual es incorrecta");
                    }

                    if (!$this->confirmPassword($newPassword, $confirmNewPassword)) {
                        throw new Exception("Las nuevas contraseñas no coinciden");
                    }

                    $this->userController->updateProfile($id, $name, $email, $newPassword);
                } else {
                    $this->userController->updateProfile($id, $name, $email);
                }

                Redirect::withSuccess('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/user/profile.php', 'Perfil actualizado correctamente');
            } catch (Exception $e) {
                Redirect::withError('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/user/edit_profile.php', $e->getMessage());
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
