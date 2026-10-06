# 09 — DOM-based XSS

## Endpoint
GET /products.php?highlight=...

## Vulnerability
JavaScript on the page reads the `highlight` query parameter and inserts
it into the DOM without escaping. The server never sees the payload — the
injection happens entirely client-side.

## Vulnerable code
File: public/products.php (inline <script>)

    var value  = new URLSearchParams(window.location.search).get('highlight');
    var output = document.getElementById('highlight-output');

    output.innerHTML = value;   // VULN: parses payload as HTML

Unlike reflected XSS, the server never echoes the payload. The server
returns an innocuous page; the JS is what makes it dangerous.

## Attack
VULNERABLE mode URL:

    /products.php?highlight=<img src=x onerror=alert('DOM-XSS')>

The <img> fails to load, `onerror` fires, alert runs.

Also works:

    /products.php?highlight=<svg onload=alert(1)>

## Defense (secure mode)
Same JS, different sink:

    output.textContent = value;   // SECURE: treated as plain text

textContent never parses HTML, so any tags remain literal characters.

## Why this matters
DOM XSS bypasses server-side escaping. Even if your server uses
htmlspecialchars(), it doesn't matter because the payload was never sent
to the server. The fix must be in the JavaScript.

## Verified
- VULNERABLE: alert fires from URL payload.
- SECURE:     payload shown as literal text.
