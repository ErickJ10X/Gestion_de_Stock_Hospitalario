<?php

namespace util;

require_once __DIR__ . '/../model/enum/RolEnum.php';
require_once __DIR__ . '/Redirect.php';
require_once __DIR__ . '/Session.php';

use model\enum\RolEnum;

class AuthGuard
{
    private Session $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    public function requireAuth()
    {
        if (!$this->session->isLoggedIn()) {
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/login.php');
            exit;
        }
        return true;
    }

    public function requireNoAuth()
    {
        if ($this->session->isLoggedIn()) {
            Redirect::toHome();
            exit;
        }
        return true;
    }

    public function requireUsuarioBotiquin()
    {
        $this->requireAuth();
        $userRole = $this->session->getUserData('rol');
        $rolAllowed = [
            RolEnum::USUARIO_BOTIQUIN,
            RolEnum::ADMINISTRADOR,
            RolEnum::GESTOR_HOSPITAL,
            RolEnum::GESTOR_PLANTA,
            RolEnum::GESTOR_GENERAL
        ];
        if (!in_array($userRole, $rolAllowed)) {
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/error/403.php');
            exit;
        }
        return true;
    }

    public function requirePlantaGestor()
    {
        $this->requireAuth();
        $userRole = $this->session->getUserData('rol');
        $rolAllowed = [
            RolEnum::GESTOR_PLANTA,
            RolEnum::ADMINISTRADOR,
            RolEnum::GESTOR_HOSPITAL,
            RolEnum::GESTOR_GENERAL
        ];
        if (!in_array($userRole, $rolAllowed)) {
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/error/403.php');
            exit;
        }
        return true;
    }

    public function requireHospitalGestor()
    {
        $this->requireAuth();
        $userRole = $this->session->getUserData('rol');
        $rolAllowed = [
            RolEnum::GESTOR_HOSPITAL,
            RolEnum::ADMINISTRADOR,
            RolEnum::GESTOR_GENERAL
        ];
        if (!in_array($userRole, $rolAllowed)) {
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/error/403.php');
            exit;
        }

        return true;
    }

    public function requireGeneralGestor()
    {
        $this->requireAuth();
        $userRole = $this->session->getUserData('rol');
        $rolAllowed = [
            RolEnum::GESTOR_GENERAL,
            RolEnum::ADMINISTRADOR
        ];
        if (!in_array($userRole, $rolAllowed)) {
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/error/403.php');
            exit;
        }
        return true;
    }

    public function requireAdministrador()
    {
        $this->requireAuth();
        $userRole = $this->session->getUserData('rol');

        if ($userRole !== RolEnum::ADMINISTRADOR) {
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/error/403.php');
            exit;
        }
        return true;
    }
}
