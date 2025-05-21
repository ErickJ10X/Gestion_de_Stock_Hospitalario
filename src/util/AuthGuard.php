<?php

namespace util;
include_once(__DIR__ . '/Session.php');
include_once(__DIR__ . '/Redirect.php');
require_once(__DIR__ . '/../model/enum/RolEnum.php');

use model\enum\RolEnum;


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

        if ($this->session->get('rol') !== RolEnum::ADMINISTRADOR) {
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
            RolEnum::ADMINISTRADOR, 
            RolEnum::GESTOR_GENERAL, 
            RolEnum::GESTOR_HOSPITAL
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
            RolEnum::ADMINISTRADOR, 
            RolEnum::GESTOR_GENERAL,
            RolEnum::GESTOR_HOSPITAL
        ];
        
        $userRole = $this->session->get('rol');
        
        if (!in_array($userRole, $allowedRoles)) {
            $this->session->setMessage("error", "No tienes permisos para gestionar plantas");
            Redirect::toHome();
        }
    }
    
    // Método necesario para verificación de sesión en los controladores
    public function checkSession(): void {
        $this->requireAuth();
    }
}
