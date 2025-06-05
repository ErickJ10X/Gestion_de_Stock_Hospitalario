<?php

namespace util;

require_once __DIR__ . '/Session.php';
require_once __DIR__ . '/Redirect.php';
require_once __DIR__ . '/../model/enum/RolEnum.php';

use model\enum\RolEnum;

class AuthGuard {
    private Session $session;
    
    public function __construct() {
        $this->session = new Session();
    }
    
    /**
     * Verifica que el usuario esté autenticado, de lo contrario redirige al login
     */
    public function requireAuth(): void {
        if (!$this->session->isLoggedIn()) {
            Redirect::toLogin();
            exit;
        }
    }
    
    /**
     * Verifica que el usuario NO esté autenticado, de lo contrario redirige al dashboard
     */
    public function requireNoAuth(): void {
        if ($this->session->isLoggedIn()) {
            $rol = $this->session->getUserData('rol');
            Redirect::toDashboard($rol);
            exit;
        }
    }
    
    /**
     * Verifica que el usuario tenga el rol de Administrador
     */
    public function requireAdministrador(): void {
        $this->requireAuth();
        
        $rol = $this->session->getUserData('rol');
        if ($rol !== RolEnum::ADMINISTRADOR) {
            $this->denyAccess();
        }
    }
    
    /**
     * Verifica que el usuario tenga el rol de Gestor General o superior
     */
    public function requireGestorGeneral(): void {
        $this->requireAuth();
        
        $rol = $this->session->getUserData('rol');
        if ($rol !== RolEnum::ADMINISTRADOR && $rol !== RolEnum::GESTOR_GENERAL) {
            $this->denyAccess();
        }
    }
    
    /**
     * Verifica que el usuario tenga el rol de Gestor de Hospital o superior
     */
    public function requireGestorHospital(): void {
        $this->requireAuth();
        
        $rol = $this->session->getUserData('rol');
        if ($rol !== RolEnum::ADMINISTRADOR && 
            $rol !== RolEnum::GESTOR_GENERAL && 
            $rol !== RolEnum::GESTOR_HOSPITAL) {
            $this->denyAccess();
        }
    }
    
    /**
     * Verifica que el usuario tenga el rol de Gestor de Planta o superior
     */
    public function requireGestorPlanta(): void {
        $this->requireAuth();
        
        $rol = $this->session->getUserData('rol');
        if ($rol !== RolEnum::ADMINISTRADOR && 
            $rol !== RolEnum::GESTOR_GENERAL && 
            $rol !== RolEnum::GESTOR_HOSPITAL &&
            $rol !== RolEnum::GESTOR_PLANTA) {
            $this->denyAccess();
        }
    }
    
    /**
     * Redirecciona a la página de acceso denegado
     */
    private function denyAccess(): void {
        Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/error/403.php');
        exit;
    }
}
