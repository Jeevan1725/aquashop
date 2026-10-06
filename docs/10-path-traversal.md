# 10 — Path Traversal

## Endpoint
GET /product.php?img=<filename>

## Vulnerability
The image proxy concatenates user input onto the uploads directory path
without checking for traversal sequences. `../` lets an attacker walk up
out of the uploads directory and read any file the web server can read.

## Vulnerable code
File: public/product.php

    $baseDir = __DIR__ . '/uploads/products/';
    $file = $baseDir . $img;   // VULN: no canonicalization
    readfile($file);

## Attack
VULNERABLE mode URL:

    /product.php?img=../../../app/config/config.local.php

Resolved path:
    /var/www/html/aquashop/public/uploads/products/../../../app/config/config.local.php
    = /var/www/html/aquashop/app/config/config.local.php

Response reveals DB credentials (db_user, db_pass) in plain text.

Also works:

    /product.php?img=../../../../../../../etc/passwd

## Defense (secure mode)
    $real = realpath($baseDir . $img);

    if ($real === false || strpos($real, realpath($baseDir)) !== 0) {
        http_response_code(404);
        die("Image not found");
    }

realpath() resolves ../ segments. The strpos check ensures the resulting
real path is still inside the uploads directory.

Additional hardening:
- Whitelist allowed extensions (.jpg, .png, .webp)
- Serve uploads via a static web server instead of PHP
- Store uploads outside the web root
- Apply basename() to strip directory components

## Verified
- VULNERABLE: ?img=../../../app/config/config.local.php leaks DB credentials.
- SECURE:     same URL returns "Image not found".
