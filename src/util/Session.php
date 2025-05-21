<?php
namespace util;
class Session {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }

    public function get(string $key) {
        return $_SESSION[$key] ?? null;
    }

    public function destroy(): void {
        session_unset();
        session_destroy();
    }

    public function isAuthenticated(): bool {
        return !empty($_SESSION['id']);
    }

    public function isAdmin(): bool {
        return $this->isAuthenticated() && $this->get('rol') === 'admin';
    }
    
    public function setMessage(string $type, string $message): void {
        $_SESSION['messages'][$type] = $message;
    }
    
    public function getMessage(string $type = null): ?string {
        if ($type === null) {
            foreach ($_SESSION['messages'] ?? [] as $type => $message) {
                $msg = $message;
                unset($_SESSION['messages'][$type]);
                return $msg;
            }
            return null;
        }
        
        $message = $_SESSION['messages'][$type] ?? null;
        unset($_SESSION['messages'][$type]);
        return $message;
    }
    
    public function getMessageType(): ?string {
        if (!isset($_SESSION['messages']) || empty($_SESSION['messages'])) {
            return null;
        }
        
        reset($_SESSION['messages']);
        return key($_SESSION['messages']);
    }
    
    public function hasMessage(string $type = null): bool {
        if ($type === null) {
            return !empty($_SESSION['messages']);
        }
        return isset($_SESSION['messages'][$type]);
    }
    
    public function clearMessage(string $type = null): void {
        if ($type === null) {
            unset($_SESSION['messages']);
        } else {
            unset($_SESSION['messages'][$type]);
        }
    }
}
