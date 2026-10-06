# 07 — CSRF (Cross-Site Request Forgery)

## Endpoints affected
- POST/GET /address-delete.php
- POST/GET /admin/order-update.php

## Vulnerability
In vulnerable mode, the app accepts state-changing requests from any origin,
with any method, and does not verify a CSRF token.

## Vulnerable code
File: app/services/CSRF.php

    public static function verify($token): bool
    {
        if (is_vulnerable()) {
            return true;   // VULN: any token accepted
        }
        ...
    }

File: public/address-delete.php

    $addressId = (int)($_REQUEST['address_id'] ?? 0);
    // VULN: accepts GET or POST, no token check

## Attack
The attacker hosts a page containing hidden iframes / images that hit the
target endpoints using the victim's cookies:

    <iframe src="http://localhost:8000/address-delete.php?address_id=1"></iframe>
    <iframe src="http://localhost:8000/admin/order-update.php?order_id=1&status=cancelled"></iframe>

See docs/csrf-attack.html for a working demo page.

Steps:
1. Log in to AquaShop as Alice.
2. Serve docs/csrf-attack.html (e.g. open the file in the browser).
3. The iframes fire silently.
4. Alice's address is deleted / order cancelled — without her clicking anything.

## Defense (secure mode)
- CSRF token generated per session: app/services/CSRF.php → generate()
- Every state-changing form includes a hidden input named csrf_token
- Server verifies with hash_equals() before processing
- State-changing endpoints require POST (no GET)

    <input type="hidden" name="csrf_token" value="<?= CSRF::generate() ?>">

    CSRF::verify($_POST['csrf_token'] ?? '');

Because the attacker page cannot read the victim's session cookie or the
CSRF token, its forged request fails.

## Verified
- VULNERABLE: attack page deletes an address / cancels an order. ✅
- SECURE:     same attack page → "Invalid CSRF Token" (or "Invalid request"). ✅
