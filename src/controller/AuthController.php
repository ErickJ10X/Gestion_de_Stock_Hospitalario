<?php

namespace controller;

use util\Redirect;
use util\Session;
use util\AuthGuard;
use Exception;
use model\enum\RolEnum;
use model\service\UsuarioService;

include_once(__DIR__ . '/../util/Session.php');
include_once(__DIR__ . '/../util/Redirect.php');
include_once(__DIR__ . '/../util/AuthGuard.php');
require_once(__DIR__ . '/../model/service/UsuarioService.php');
require_once(__DIR__ . '/../model/enum/RolEnum.php');

class AuthController {
    private Session $session;
    private UsuarioService $usuarioService;
    private AuthGuard $authGuard;

    public function __construct(){
        $this->session = new Session();
        $this->usuarioService = new UsuarioService();
        $this->authGuard = new AuthGuard();
    }

    public function login(): void {
        $this->authGuard->requireNoAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->sanitizar($_POST['email'] ?? '');
            $password = $_POST['contrasena'] ?? '';

            try {
                $usuario = $this->usuarioService->login($email, $password);
                if ($usuario) {
                    // Guardar datos del usuario en sesión
                    $this->session->setUserData([
                        'id' => $usuario->getIdUsuario(),
                        'nombre' => $usuario->getNombre(),
                        'email' => $usuario->getEmail(),
                        'rol' => $usuario->getRol()
                    ]);
                    
                    $ubicaciones = $usuario->getUbicaciones();
                    if (!empty($ubicaciones)) {
                        $this->session->set('ubicaciones', $ubicaciones);
                        Redirect::toHome();
                    } else {
                        $rolRequiereUbicacion = in_array($usuario->getRol(), [
                            RolEnum::USUARIO_BOTIQUIN,
                            RolEnum::GESTOR_PLANTA
                        ]);

                        if ($rolRequiereUbicacion) {
                            Redirect::withWarning('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/user/profile.php', 'Tu cuenta no tiene ubicaciones asignadas. Contacta con un administrador.');
                        } else {
                            Redirect::toHome();
                        }
                    }
                } else {
                    throw new Exception("Correo electrónico o contraseña incorrectos");
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

    public function sanitizar($input): string{
        $input = trim($input);
        $input = stripslashes($input);
        return htmlspecialchars($input);
    }

    public function confirmPassword($password, $confirmPassword): bool{
        return $password === $confirmPassword;
    }
}
