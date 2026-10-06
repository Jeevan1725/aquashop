<?php

class CSRF
{
    public static function generate(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }


    public static function verify($token): bool
    {
        if (is_vulnerable()) {
            return true; // VULN: CSRF check disabled
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (
            empty($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], (string)$token)
        ) {
            die("Invalid CSRF Token");
        }

        return true;
    }
}
