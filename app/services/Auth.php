<?php

class Auth
{

    public static function login(array $user)
    {

        session_start();

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

    }



    public static function logout()
    {

        session_start();

        session_destroy();

    }



    public static function check(): bool
    {

        session_start();

        return isset($_SESSION['user']);

    }



    public static function user(): ?array
    {

        session_start();

        return $_SESSION['user'] ?? null;

    }

}
