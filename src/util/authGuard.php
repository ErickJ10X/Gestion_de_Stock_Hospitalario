<?php
include_once(__DIR__ . '/session.php');
include_once(__DIR__ . '/redirect.php');
require_once(__DIR__ . '/../model/enum/RolEnum.php');

use App\model\enum\RolEnum;

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

        if ($this->session->get('rol') !== RolEnum::ADMINISTRADOR->value) {
            Redirect::withError('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/index.php', 'No tienes permisos para acceder a esta página');
        }
    }

    public function requireRole(array $allowedRoles): void {
        $this->requireAuth();
        
        $userRole = $this->session->get('rol');
        
        if (!in_array($userRole, $allowedRoles)) {
            $this->session->setMessage("error", "No tienes permisos para acceder a esta sección");
            Redirect::toHome();
        }
    }

    public function requireHospitalGestor(): void {
        $this->requireAuth();
        
        $allowedRoles = [
            RolEnum::ADMINISTRADOR->value, 
            RolEnum::GESTOR_GENERAL->value, 
            RolEnum::GESTOR_HOSPITAL->value
        ];
        
        $userRole = $this->session->get('rol');
        
        if (!in_array($userRole, $allowedRoles)) {
            $this->session->setMessage("error", "No tienes permisos para gestionar hospitales");
            Redirect::toHome();
        }
    }

    public function requirePlantaGestor(): void {
        $this->requireAuth();
        
        $allowedRoles = [
            RolEnum::ADMINISTRADOR->value, 
            RolEnum::GESTOR_GENERAL->value,
            RolEnum::GESTOR_HOSPITAL->value
        ];
        
        $userRole = $this->session->get('rol');
        
        if (!in_array($userRole, $allowedRoles)) {
            $this->session->setMessage("error", "No tienes permisos para gestionar plantas");
            Redirect::toHome();
        }
    }
}
