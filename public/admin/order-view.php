<?php

require_once __DIR__ . '/../../app/services/AdminAuth.php';
AdminAuth::check();

require_once __DIR__ . '/../../app/bootstrap.php';
require_once __DIR__ . '/../../app/services/CSRF.php';

$orderId = (int)($_GET['id'] ?? 0);

if ($orderId <= 0) {
    die("Invalid order");
}

$stmt = $db->prepare("
    SELECT
        o.*,
        u.name AS customer,
        u.email,
        a.full_name,
        a.phone,
        a.address_line1,
        a.city,
        a.state,
        a.postal_code
    FROM orders o
    INNER JOIN users u ON o.user_id = u.id
    INNER JOIN addresses a ON o.address_id = a.id
    WHERE o.id = ?
");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    die("Order not found");
}

$stmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
<title>Order View</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php require_once __DIR__ . "/../../app/views/navbar.php"; ?>

<section class="order-container">

<h1>Order Details 📦</h1>

<div class="order-card">
    <h2><?= e($order['order_number']) ?></h2>
    <p>Customer: <?= e($order['customer']) ?></p>
    <p>Email: <?= e($order['email']) ?></p>
</div>

<div class="order-card">
    <h2>Delivery Address</h2>
    <p><?= e($order['full_name']) ?></p>
    <p><?= e($order['address_line1']) ?></p>
    <p><?= e($order['city']) ?>, <?= e($order['state']) ?></p>
</div>

<div class="order-card">
    <h2>Products</h2>
    <?php foreach ($items as $item): ?>
        <p>
            <?= e($item['product_name']) ?><br>
            Quantity: <?= (int)$item['quantity'] ?><br>
            Subtotal: ₹<?= number_format($item['subtotal'], 2) ?>
        </p>
        <hr>
    <?php endforeach; ?>
</div>

<div class="order-card">
    <h2>Payment Summary</h2>
    <p>Total: ₹<?= number_format($order['total'], 2) ?></p>
    <p>Current Status: <b><?= e($order['status']) ?></b></p>

    <?php if (is_vulnerable()): ?>
        <p style="background:#fff3cd;color:#856404;padding:10px;border-radius:6px;">
            ⚠️ <strong>VULNERABLE MODE</strong> — status update accepts GET (CSRF possible).
        </p>
    <?php endif; ?>

    <form method="POST" action="order-update.php">
        <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">

        <?php if (is_secure()): ?>
            <input type="hidden" name="csrf_token" value="<?= CSRF::generate() ?>">
        <?php endif; ?>

        <select name="status">
            <?php
            $statuses = [
                'pending' => 'Pending',
                'confirmed' => 'Confirmed',
                'processing' => 'Processing',
                'packed' => 'Packed',
                'shipped' => 'Shipped',
                'out_for_delivery' => 'Out For Delivery',
                'delivered' => 'Delivered',
                'cancelled' => 'Cancelled'
            ];
            foreach ($statuses as $key => $label):
            ?>
                <option value="<?= $key ?>" <?= $order['status'] === $key ? 'selected' : '' ?>>
                    <?= $label ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button>Update Status</button>
    </form>
</div>

</section>

</body>
</html>
