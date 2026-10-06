# 06 — IDOR: Order Details & Invoice

## Endpoints
- GET /order-details.php?id=X
- GET /order-invoice.php?id=X

## Vulnerability
Vulnerable mode fetches an order by ID only — no user_id filter. The same
applies to the printable invoice.

## Attack
1. Log in as User A. Place an order → note the order ID (e.g. 12).
2. Log in as User B in a different browser.
3. Visit /order-details.php?id=12 → you see User A's order: address, phone,
   items, totals.
4. Visit /order-invoice.php?id=12 → printable invoice with User A's details.

## Defense
    $order = $orderModel->getOrderById($orderId, $userId);

Which executes:

    SELECT ... FROM orders o
    INNER JOIN addresses a ON o.address_id = a.id
    WHERE o.id = ? AND o.user_id = ?
    LIMIT 1

Both the order ID and the owner must match.

## Verified
- VULNERABLE: cross-user order view succeeds. ✅
- SECURE:     "Order not found".               ✅
