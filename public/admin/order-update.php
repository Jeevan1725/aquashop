<?php

require_once __DIR__ . '/../../app/services/AdminAuth.php';
AdminAuth::check();

require_once __DIR__ . '/../../app/bootstrap.php';
require_once __DIR__ . '/../../app/services/CSRF.php';

/*
|--------------------------------------------------------------------------
| VULNERABLE: accept GET or POST — no CSRF token required
| SECURE:     POST only + CSRF token verified
|--------------------------------------------------------------------------
*/
if (is_secure()) {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        die("Invalid request");
    }

    CSRF::verify($_POST['csrf_token'] ?? '');

    $orderId = (int)($_POST['order_id'] ?? 0);
    $status  = $_POST['status'] ?? '';

} else {

    // VULN
    $orderId = (int)($_REQUEST['order_id'] ?? 0);
    $status  = $_REQUEST['status'] ?? '';
}

$allowedStatuses = [
    'pending', 'confirmed', 'processing', 'packed',
    'shipped', 'out_for_delivery', 'delivered',
    'cancelled', 'refunded'
];

if ($orderId <= 0 || !in_array($status, $allowedStatuses, true)) {
    die("Invalid status");
}

$stmt = $db->prepare("SELECT status FROM orders WHERE id = ? LIMIT 1");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    die("Order not found");
}

$oldStatus = $order['status'];

if ($oldStatus === $status) {
    header("Location: order-view.php?id=" . $orderId);
    exit;
}

try {
    $db->beginTransaction();

    $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $orderId]);

    $changedBy = $_SESSION['user']['id'] ?? null;

    $stmt = $db->prepare("
        INSERT INTO order_status_history
        (order_id, old_status, new_status, changed_by)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$orderId, $oldStatus, $status, $changedBy]);

    $db->commit();

} catch (Throwable $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    die("Unable to update order status.");
}

header("Location: order-view.php?id=" . $orderId);
exit;
