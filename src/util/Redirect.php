<?php

namespace util;

class Redirect
{
    public static function to($url)
    {
        header("Location: $url");
        exit;
    }

    public static function toHome()
    {
        self::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/index.php');
    }

    public static function toLogin()
    {
        self::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/login.php');
    }

    public static function toHospitales()
    {
        self::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/hospitales/index.php');
    }

    public static function toUsuario()
    {
        self::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/usuario/index.php');
    }

    public static  function toAlmacenes()
    {
        self::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/almacenes/index.php');
    }
    public static function toBotiquines()
    {
        self::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/botiquines/index.php');
    }
    public static function toProductos()
    {
        self::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/productos/index.php');
    }
    public static function toPactos()
    {
        self::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/pactos/index.php');
    }
    public static function toLecturaStock()
    {
        self::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/lecturaStock/index.php');
    }
    public static function toRequisiciones()
    {
        self::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/requisiciones/index.php');
    }

    public static function toEtiquetas()
    {
        self::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/etiquetas/index.php');
    }

    public static function withError($url, $message)
    {
        $session = new Session();
        $session->setMessage('error', $message);
        self::to($url);
    }

    public static function withSuccess($url, $message)
    {
        $session = new Session();
        $session->setMessage('success', $message);
        self::to($url);
    }

    public static function withWarning(string $string, string $string1)
    {
        $session = new Session();
        $session->setMessage('warning', $string1);
        self::to($string);
    }
}