# 11 — Broken Access Control (Admin)

## Endpoint
All /admin/*.php pages (index, products, orders, users, etc.)

## Vulnerability
In VULNERABLE mode, AdminAuth::check() verifies the user is LOGGED IN
but does not verify the ROLE. Any authenticated customer can access the
admin panel.

## Vulnerable code
File: app/services/AdminAuth.php

    if (!isset($_SESSION['user'])) {
        header("Location: /login.php");
        exit;
    }

    // VULN: role check missing

## Attack
1. Log in as a normal customer (register a fresh account).
2. Visit http://localhost:8000/admin/index.php
3. The admin dashboard loads — user counts, revenue, recent orders.

From here the attacker can:

    /admin/products.php        — edit / delete any product
    /admin/orders.php          — view every customer's orders
    /admin/users.php           — list every user
    /admin/user-edit.php?id=1  — change any user's role to admin

No admin credentials required.

## Defense (secure mode)
    if (is_secure()) {
        if (($_SESSION['user']['role'] ?? null) !== 'admin') {
            http_response_code(403);
            die("Access Denied — admin role required");
        }
    }

The role is stored in the session by Auth::login() at sign-in time.
Customer sessions carry role='customer' → 403 on any /admin/* request.

## Additional hardening
- Enforce role check on EVERY admin page (not just at login)
- Centralize in a middleware / router
- Log unauthorized access attempts
- Use least-privilege principles in the DB user too

## Verified
- VULNERABLE: customer session loads /admin/index.php.
- SECURE:     customer session gets "Access Denied — admin role required".
