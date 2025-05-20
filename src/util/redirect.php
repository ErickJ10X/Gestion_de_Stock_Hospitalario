<?php
class Redirect {
    public static function to(string $path, array $params = []): void {
        $url = $path;

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        header("Location: $url");
        exit;
    }

    public static function withError(string $path, string $message): void {
        self::to($path, ['error' => 1, 'message' => urlencode($message)]);
    }

    public static function withSuccess(string $path, string $message = 'success'): void {
        self::to($path, ['success' => $message]);
    }

    public static function toLogin(): void {
        self::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/src/view/auth/login.php');
    }

    public static function toHome(): void {
        self::to('/Pegasus-Medical-Gestion_de_Stock_Hospitalario/public/');
    }
}

