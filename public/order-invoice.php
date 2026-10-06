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
| VULNERABLE: no ownership check
| SECURE:     ownership enforced
|--------------------------------------------------------------------------
*/
if (is_vulnerable()) {

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
    $order = $orderModel->getOrderById($orderId, $userId);
}

if (!$order) {
    http_response_code(404);
    die("Order not found.");
}

$items = $orderModel->getOrderItems($orderId);

$stmt = $db->prepare("
    SELECT gateway, amount, status, paid_at
    FROM payments
    WHERE order_id = ?
    ORDER BY id DESC
    LIMIT 1
");
$stmt->execute([$orderId]);
$payment = $stmt->fetch();

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Invoice <?= e($order['order_number']) ?></title>
<link rel="stylesheet" href="css/style.css">
<style>
.invoice-container { width: 85%; max-width: 900px; margin: 40px auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 3px 15px rgba(0,0,0,0.08); }
.invoice-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 35px; }
.invoice-logo { font-size: 28px; font-weight: bold; color: #0b3954; }
.invoice-title { text-align: right; }
.invoice-title h1 { margin: 0; }
.invoice-section { margin: 25px 0; }
.invoice-section h2 { color: #0b3954; margin-bottom: 12px; }
.invoice-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
.invoice-table th, .invoice-table td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
.invoice-table th { background: #f4f8fb; }
.invoice-total { margin-left: auto; width: 300px; }
.invoice-total p { display: flex; justify-content: space-between; margin: 10px 0; }
.invoice-grand-total { font-size: 22px; font-weight: bold; color: #087e8b; border-top: 2px solid #ddd; padding-top: 15px; }
.invoice-actions { margin-top: 30px; display: flex; gap: 15px; }
@media print {
    .navbar, .invoice-actions { display: none; }
    body { background: white; }
    .invoice-container { width: 100%; max-width: none; margin: 0; padding: 20px; box-shadow: none; }
}
</style>
</head>
<body>

<header class="navbar">
    <div class="logo">🐠 AquaShop</div>
    <nav>
        <a href="index.php">Home</a>
        <a href="products.php">Shop</a>
        <a href="orders.php">My Orders</a>
        <a href="profile.php">Account 👤</a>
    </nav>
</header>

<div class="invoice-container">

<?php if (is_vulnerable()): ?>
    <div style="background:#fff3cd;color:#856404;padding:12px;border-radius:6px;margin-bottom:20px;">
        ⚠️ <strong>VULNERABLE MODE</strong> — this invoice belongs to a different user (IDOR).
    </div>
<?php endif; ?>

<div class="invoice-header">
    <div class="invoice-logo">
        🐠 AquaShop
        <p style="font-size:14px;font-weight:normal;">Aquarium & Ornamental Fish Shop</p>
    </div>
    <div class="invoice-title">
        <h1>INVOICE</h1>
        <p>Order: <strong><?= e($order['order_number']) ?></strong></p>
        <p>Date: <?= e($order['created_at']) ?></p>
    </div>
</div>

<hr>

<div class="invoice-section">
    <h2>Customer</h2>
    <p><strong><?= e($order['full_name']) ?></strong></p>
    <p><?= e($_SESSION['user']['email']) ?></p>
    <p><?= e($order['phone']) ?></p>
</div>

<div class="invoice-section">
    <h2>Delivery Address</h2>
    <p><?= e($order['address_line1']) ?></p>
    <?php if (!empty($order['address_line2'])): ?>
        <p><?= e($order['address_line2']) ?></p>
    <?php endif; ?>
    <p><?= e($order['city']) ?>, <?= e($order['state']) ?></p>
    <p><?= e($order['postal_code']) ?></p>
</div>

<div class="invoice-section">
    <h2>Items</h2>
    <table class="invoice-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= e($item['product_name']) ?></td>
                    <td><?= (int)$item['quantity'] ?></td>
                    <td>₹<?= number_format($item['unit_price'], 2) ?></td>
                    <td>₹<?= number_format($item['subtotal'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="invoice-total">
    <p><span>Subtotal</span><span>₹<?= number_format($order['subtotal'], 2) ?></span></p>
    <p><span>Delivery</span><span>₹<?= number_format($order['delivery_fee'], 2) ?></span></p>
    <p><span>Tax</span><span>₹<?= number_format($order['tax'], 2) ?></span></p>
    <p><span>Discount</span><span>₹<?= number_format($order['discount'], 2) ?></span></p>
    <p class="invoice-grand-total"><span>Total</span><span>₹<?= number_format($order['total'], 2) ?></span></p>
</div>

<div class="invoice-section">
    <h2>Payment</h2>
    <?php if ($payment): ?>
        <p>Gateway: <?= e($payment['gateway'] ?? 'Not specified') ?></p>
        <p>Payment Status: <strong><?= e($payment['status']) ?></strong></p>
        <?php if (!empty($payment['paid_at'])): ?>
            <p>Paid At: <?= e($payment['paid_at']) ?></p>
        <?php endif; ?>
    <?php else: ?>
        <p>Payment information not available.</p>
    <?php endif; ?>
    <p>Order Status: <strong><?= e($order['status']) ?></strong></p>
</div>

<div class="invoice-actions">
    <button class="cart-button" onclick="window.print()">🖨️ Print / Save PDF</button>
    <a class="cart-button" href="order-details.php?id=<?= (int)$order['id'] ?>">← Back to Order</a>
</div>

</div>

</body>
</html>
