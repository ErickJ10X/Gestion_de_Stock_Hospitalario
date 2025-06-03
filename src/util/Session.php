<?php

namespace util;

class Session
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    public function remove($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public function destroy()
    {
        session_unset();
        session_destroy();
    }

    public function isLoggedIn(): bool
    {
        return !empty($_SESSION['id']);
    }

    public function setUserData($user)
    {
        $_SESSION['id'] = $user->getId();
        $_SESSION['nombre'] = $user->getNombre();
        $_SESSION['email'] = $user->getEmail();
        $_SESSION['rol'] = $user->getRol();
    }

    public function getUserData($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function setMessage($type, $message)
    {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_message_type'] = $type;
    }

    public function hasMessage($type = null)
    {
        if ($type === null) {
            return isset($_SESSION['flash_message']);
        }
        return isset($_SESSION['flash_message']) && isset($_SESSION['flash_message_type']) && $_SESSION['flash_message_type'] === $type;
    }

    public function getMessage($type = null)
    {
        if (!$this->hasMessage($type)) {
            return null;
        }

        $message = $_SESSION['flash_message'];
        $messageType = $_SESSION['flash_message_type'];

        if ($type === null || $messageType === $type) {
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_message_type']);
            return $message;
        }

        return null;
    }

    /**
     * Elimina un mensaje flash específico
     *
     * @param string|null $type Tipo de mensaje a eliminar. Si es null, elimina todos los mensajes.
     * @return void
     */
    public function clearMessage($type = null)
    {
        if ($type === null) {
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_message_type']);
        } else if (
            isset($_SESSION['flash_message_type']) &&
            $_SESSION['flash_message_type'] === $type
        ) {
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_message_type']);
        }
    }

    public function getMessageType()
    {
        if (isset($_SESSION['flash_message_type'])) {
            return $_SESSION['flash_message_type'];
        }
        return null;
    }
}