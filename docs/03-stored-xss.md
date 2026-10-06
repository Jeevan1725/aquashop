# 03 — Stored XSS (Product name/description)

## Endpoint
Output rendered in: /products.php, /index.php

## Vulnerability
In vulnerable mode, product fields are echoed into HTML without escaping.

## Vulnerable code
File: public/products.php

    <h3><?= e($product['name']) ?></h3>

Where e() is defined in app/config/mode.php:

    function e(?string $value): string {
        if (is_secure()) {
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        return $value; // VULN: no escaping
    }

## Attack
1. Toggle to VULNERABLE mode.
2. Login as admin (can use SQLi bypass from doc 01).
3. /admin/add-product.php:
     Name:        <script>alert('XSS-NAME')</script>
     Description: <img src=x onerror="alert('XSS-DESC')">
4. Save.
5. Visit /products.php → both payloads execute for every visitor.

## Why it works
The stored string is echoed raw into HTML. The browser parses the tags
and runs the script. Because it is stored in the database, every visitor
triggers it — no per-victim interaction needed.

## Defense
In secure mode, e() calls htmlspecialchars() with ENT_QUOTES and UTF-8,
converting < > " ' & into HTML entities. Browser renders them as text.

## Verified
- VULNERABLE: two alerts fire. ✅
- SECURE:     payload shown as literal text. ✅
