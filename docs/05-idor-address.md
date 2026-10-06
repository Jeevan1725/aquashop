# 05 — IDOR: Address Edit

## Endpoint
GET/POST /address-edit.php?id=X

## Vulnerability
Vulnerable mode fetches and updates an address by ID only — no user_id filter.

## Vulnerable code
    // VULN
    $stmt = $db->prepare("SELECT * FROM addresses WHERE id = ? LIMIT 1");
    $stmt->execute([$addressId]);

    // VULN update
    UPDATE addresses SET ... WHERE id = ?

## Attack
1. Log in as User A.
2. Create one address → note its ID (e.g. 5).
3. Log in as User B in another browser.
4. Visit /address-edit.php?id=5 → User A's address loads.
5. Change any field, save → User A's address is modified.

User B never had permission to touch that record.

## Defense
    $address = $addressModel->getById($addressId, $userId);

And in SQL:

    SELECT * FROM addresses WHERE id = ? AND user_id = ? LIMIT 1

Now ID + owner are both required. Attacker with User B's session can only
edit User B's addresses.

## Verified
- VULNERABLE: cross-user edit succeeds. ✅
- SECURE:     "Address not found".       ✅
