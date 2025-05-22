<?php

namespace util;

require_once __DIR__ . '/../model/enum/RolEnum.php';
require_once __DIR__ . '/Redirect.php';
require_once __DIR__ . '/Session.php';

use model\enum\RolEnum;

class AuthGuard
{
    private Session $session;
    private RolEnum $rolEnum;

    public function __construct()
    {
        $this->session = new Session();
        $this->rolEnum = new RolEnum();
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
            $this->rolEnum::USUARIO_BOTIQUIN,
            $this->rolEnum::ADMINISTRADOR,
            $this->rolEnum::GESTOR_HOSPITAL,
            $this->rolEnum::GESTOR_PLANTA,
            $this->rolEnum::GESTOR_GENERAL
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
            $this->rolEnum::GESTOR_PLANTA,
            $this->rolEnum::ADMINISTRADOR,
            $this->rolEnum::GESTOR_HOSPITAL,
            $this->rolEnum::GESTOR_GENERAL
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
            $this->rolEnum::GESTOR_HOSPITAL,
            $this->rolEnum::ADMINISTRADOR,
            $this->rolEnum::GESTOR_GENERAL
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
            $this->rolEnum::GESTOR_GENERAL,
            $this->rolEnum::ADMINISTRADOR
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

        if ($userRole !==  $this->rolEnum::ADMINISTRADOR) {
            Redirect::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/error/403.php');
            exit;
        }
        return true;
    }
}
