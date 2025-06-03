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