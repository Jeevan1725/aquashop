<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/Order.php';

$orderId = (int)($_GET['id'] ?? 0);

if ($orderId <= 0) {
    die("Invalid order.");
}

$userId = (int)$_SESSION['user']['id'];

$orderModel = new Order($db);

/*
|--------------------------------------------------------------------------
| VULNERABLE: no ownership check — IDOR possible
| SECURE:     ownership enforced by getOrderById()
|--------------------------------------------------------------------------
*/
if (is_vulnerable()) {

    // VULN: fetch by ID only
    $stmt = $db->prepare("
        SELECT
            o.id,
            o.order_number,
            o.subtotal,
            o.delivery_fee,
            o.tax,
            o.discount,
            o.total,
            o.status,
            o.created_at,
            a.full_name,
            a.phone,
            a.address_line1,
            a.address_line2,
            a.city,
            a.state,
            a.postal_code
        FROM orders o
        INNER JOIN addresses a ON o.address_id = a.id
        WHERE o.id = ?
        LIMIT 1
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch() ?: null;

} else {

    // SECURE: ownership enforced
    $order = $orderModel->getOrderById($orderId, $userId);
}

if (!$order) {
    http_response_code(404);
    die("Order not found.");
}

$items = $orderModel->getOrderItems($orderId);


$statusLabels = [
    'pending' => 'Order Placed',
    'confirmed' => 'Confirmed',
    'processing' => 'Processing',
    'packed' => 'Packed',
    'shipped' => 'Shipped',
    'out_for_delivery' => 'Out For Delivery',
    'delivered' => 'Delivered',
    'cancelled' => 'Cancelled',
    'refunded' => 'Refunded'
];


$historyStmt = $db->prepare("
    SELECT
        old_status,
        new_status,
        created_at
    FROM order_status_history
    WHERE order_id = ?
    ORDER BY created_at ASC, id ASC
");
$historyStmt->execute([$orderId]);
$history = $historyStmt->fetchAll();


$timelineStatuses = [
    'pending',
    'confirmed',
    'processing',
    'packed',
    'shipped',
    'out_for_delivery',
    'delivered'
];


$historyByStatus = [];

foreach ($history as $entry) {
    $historyByStatus[$entry['new_status']] = $entry['created_at'];
}

if (!isset($historyByStatus['pending'])) {
    $historyByStatus['pending'] = $order['created_at'];
}

$currentStatus = $order['status'];

?>
<!DOCTYPE html>
<html>
<head>
<title>Order Details | AquaShop</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="navbar">
    <div class="logo">🐠 AquaShop</div>
    <nav>
        <a href="index.php">Home</a>
        <a href="products.php">Shop</a>
        <a href="cart.php">Cart 🛒</a>
        <a href="profile.php">Account 👤</a>
    </nav>
</header>

<section class="order-container">

<h1>📦 Order Details</h1>

<?php if (is_vulnerable()): ?>
    <div class="order-card" style="background:#fff3cd;color:#856404;">
        ⚠️ <strong>VULNERABLE MODE</strong> — no ownership check on this order ID.
    </div>
<?php endif; ?>

<div class="order-card">
    <h2>Order # <?= e($order['order_number']) ?></h2>
    <p>Date: <?= e($order['created_at']) ?></p>
    <p>Status:
        <strong>
            <?= e($statusLabels[$currentStatus] ?? $currentStatus) ?>
        </strong>
    </p>
</div>

<div class="order-card tracking-history">
    <h2>🚚 Order Timeline</h2>
    <?php foreach ($timelineStatuses as $status): ?>
        <?php
        $isCompleted = isset($historyByStatus[$status]);
        $label = $statusLabels[$status];
        $timestamp = $historyByStatus[$status] ?? null;
        ?>
        <div class="timeline-item">
            <div class="timeline-icon">
                <?php if ($isCompleted): ?>✅<?php else: ?>⭕<?php endif; ?>
            </div>
            <div class="timeline-content">
                <strong><?= e($label) ?></strong>
                <?php if ($timestamp): ?>
                    <p><?= e(date('d M Y, h:i A', strtotime($timestamp))) ?></p>
                <?php else: ?>
                    <p class="waiting">Waiting</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="order-card">
    <h2>📍 Delivery Address</h2>
    <p><?= e($order['full_name']) ?></p>
    <p><?= e($order['phone']) ?></p>
    <p><?= e($order['address_line1']) ?></p>
    <?php if (!empty($order['address_line2'])): ?>
        <p><?= e($order['address_line2']) ?></p>
    <?php endif; ?>
    <p><?= e($order['city']) ?>, <?= e($order['state']) ?></p>
    <p><?= e($order['postal_code']) ?></p>
</div>

<div class="order-card">
    <h2>🐟 Items</h2>
    <?php foreach ($items as $item): ?>
        <div class="item-box">
            <h3><?= e($item['product_name']) ?></h3>
            <p>Quantity: <?= (int)$item['quantity'] ?></p>
            <p>Unit Price: ₹<?= number_format($item['unit_price'], 2) ?></p>
            <p>Subtotal: ₹<?= number_format($item['subtotal'], 2) ?></p>
        </div>
        <hr>
    <?php endforeach; ?>
</div>

<div class="order-card">
    <h2>💳 Payment Summary</h2>
    <p>Subtotal: ₹<?= number_format($order['subtotal'], 2) ?></p>
    <p>Delivery: ₹<?= number_format($order['delivery_fee'], 2) ?></p>
    <p>Tax: ₹<?= number_format($order['tax'], 2) ?></p>
    <p>Discount: ₹<?= number_format($order['discount'], 2) ?></p>
    <h2>Total: ₹<?= number_format($order['total'], 2) ?></h2>
</div>

<div style="display:flex; gap:15px; flex-wrap:wrap;">
    <a class="cart-button" href="order-invoice.php?id=<?= (int)$order['id'] ?>">
        🧾 View / Print Invoice
    </a>
    <a class="cart-button" href="orders.php">
        ← Back to My Orders
    </a>
</div>

</section>

</body>
</html>
