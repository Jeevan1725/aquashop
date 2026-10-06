# 01 — SQL Injection: Login Bypass

## Endpoint
POST /login.php

## Vulnerability
The `email` field is concatenated directly into a SQL query.

## Vulnerable code
File: `app/models/User.php` → `findByEmailVulnerable()`

    $sql = "SELECT ... WHERE u.email = '$email' LIMIT 1";
    $stmt = $this->db->query($sql);

## Attack
1. Toggle mode to VULNERABLE (red pill in navbar).
2. Go to /login.php.
3. Email:  ' OR '1'='1' #
4. Password: anything
5. Click Login → logged in as first user (admin).

## Why it works
Final SQL:

    SELECT ... WHERE u.email = '' OR '1'='1' #' LIMIT 1

`#` comments out the trailing quote and LIMIT; `'1'='1'` is always true;
first row returned; session set; auth bypassed.

## Defense (secure mode)
File: `app/models/User.php` → `findByEmail()`

    $stmt = $this->db->prepare("... WHERE u.email = ? LIMIT 1");
    $stmt->execute([$email]);
    password_verify($password, $user['password_hash']);

Parameterized query + password hash verification. Payload is treated
as a literal string → no matching row → "Invalid email or password".

## Verified
- VULNERABLE: payload logs in. ✅
- SECURE:     same payload rejected. ✅
