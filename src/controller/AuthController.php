<?php

namespace controller;

use util\Redirect;
use util\Session;
use util\AuthGuard;
use Exception;

include_once(__DIR__ . '/../util/Session.php');
include_once(__DIR__ . '/../util/Redirect.php');
include_once(__DIR__ . '/../util/AuthGuard.php');
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
                    // Verificamos si el usuario tiene ubicaciones asignadas
                    // y si su rol lo requiere, redirigimos a seleccionar ubicación
                    if (isset($user['ubicaciones']) && !empty($user['ubicaciones'])) {
                        Redirect::toHome();
                    } else {
                        // Si el usuario no tiene ubicaciones asignadas pero debería tenerlas según su rol
                        $rolRequiereUbicacion = in_array($user['rol'], ['Usuario de botiquín', 'Gestor de planta']);
                        if ($rolRequiereUbicacion) {
                            Redirect::withWarning('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/user/profile.php', 'Tu cuenta no tiene ubicaciones asignadas. Contacta con un administrador.');
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
                // Verifica y obtiene el ID numérico del rol en lugar del texto
                $rolNumerico = $this->userController->getRolIdByName($rol);
                
                // Registra el usuario con el ID del rol
                $this->userController->register($name, $email, $password, $rolNumerico);
                
                // Si tiene acceso a la selección de ubicaciones, redirigir a la página para asignar ubicaciones
                if ($this->session->isLoggedIn() && $this->session->getUserData('rol') == 'Administrador') {
                    Redirect::withSuccess('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/admin/usuarios.php', 'Usuario registrado correctamente. Recuerda asignarle ubicaciones si es necesario.');
                } else {
                    Redirect::withSuccess('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/login.php', 'Usuario registrado correctamente');
                }
            } catch (Exception $e) {
                Redirect::withError('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/register.php', $e->getMessage());
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
                // Si se está cambiando la contraseña
                if (!empty($newPassword)) {
                    // Verificar que la contraseña actual es correcta
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
