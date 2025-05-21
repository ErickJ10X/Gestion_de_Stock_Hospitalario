<?php
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
    
    public function getMessage(string $type): ?string {
        $message = $_SESSION['messages'][$type] ?? null;
        unset($_SESSION['messages'][$type]);
        return $message;
    }
    
    public function hasMessage(string $type): bool {
        return isset($_SESSION['messages'][$type]);
    }
}
