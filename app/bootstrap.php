<?php

require_once __DIR__ . '/config/mode.php';
require_once __DIR__ . '/config/database.php';

/*
|--------------------------------------------------------------------------
| Clickjacking defense (applied globally)
|--------------------------------------------------------------------------
|
| In SECURE mode, send headers that prevent the site from being embedded
| inside an iframe on another origin.
|
| In VULNERABLE mode, no headers are sent — clickjacking is possible.
|
*/
if (is_secure()) {

    header('X-Frame-Options: DENY');

    header("Content-Security-Policy: frame-ancestors 'none'");

    header('X-Content-Type-Options: nosniff');
}

$database = new Database();
$db = $database->connect();
