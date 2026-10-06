# 04 — Reflected XSS (Search)

## Endpoint
GET /products.php?search=...

## Vulnerability
The search string is echoed back into the input value attribute.

## Vulnerable code
File: public/products.php

    <input
        type="text"
        name="search"
        value="<?= e($search ?? '') ?>"
    >

Where e() is a no-op in vulnerable mode.

## Attack
VULNERABLE mode URL:

    /products.php?search="><script>alert('REFLECTED')</script>

The `">` closes the value attribute and the <input> tag, and the
<script> runs.

## Defense
Secure mode: e() runs htmlspecialchars(). Payload becomes harmless text:
    &quot;&gt;&lt;script&gt;alert('REFLECTED')&lt;/script&gt;

## Verified
- VULNERABLE: alert fires. ✅
- SECURE:     payload shown as escaped text. ✅
