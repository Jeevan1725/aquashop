<?php

require_once __DIR__ . '/../config/mode.php';

class AdminAuth
{
    /*
    |--------------------------------------------------------------------------
    | Enforce admin access.
    |
    | SECURE:     user must exist AND role must be 'admin'
    | VULNERABLE: any logged-in user is allowed (BAC demo)
    |--------------------------------------------------------------------------
    */
    public static function check()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            header("Location: /login.php");
            exit;
        }

        if (is_secure()) {
            if (($_SESSION['user']['role'] ?? null) !== 'admin') {
                http_response_code(403);
                die("Access Denied — admin role required");
            }
        }

        // VULN: no role check
    }
}
