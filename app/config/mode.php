<?php

/*
|--------------------------------------------------------------------------
| Security Mode
|--------------------------------------------------------------------------
|
| VULNERABLE : intentionally insecure code paths are used (for demo)
| SECURE     : hardened code paths are used (production-safe)
|
| Stored in the session so the toggle survives navigation.
|
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['mode'])) {
    $_SESSION['mode'] = 'vulnerable';
}

function is_secure(): bool
{
    return ($_SESSION['mode'] ?? 'vulnerable') === 'secure';
}

function is_vulnerable(): bool
{
    return !is_secure();
}

function current_mode(): string
{
    return $_SESSION['mode'] ?? 'vulnerable';
}

function toggle_mode(): string
{
    $_SESSION['mode'] = is_secure() ? 'vulnerable' : 'secure';
    return $_SESSION['mode'];
}

/**
 * Output-escape helper.
 * SECURE   -> htmlspecialchars (safe)
 * VULNERABLE -> returns value untouched (XSS-able)
 */
function e(?string $value): string
{
    if ($value === null) {
        return '';
    }

    if (is_secure()) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    return $value; // VULN: no escaping
}
