<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/Order.php';

$orderModel = new Order($db);
$userId = (int)$_SESSION['user']['id'];
$orders = $orderModel->getOrdersByUser($userId);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Orders | AquaShop</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require_once __DIR__ . '/../app/views/navbar.php'; ?>

<section class="page-section">

    <div class="page-header">
        <span class="eyebrow small">MY ACCOUNT</span>
        <h1>My Orders</h1>
        <p>Welcome back, <?= e($_SESSION['user']['name']) ?>. Here's your order history.</p>
    </div>

    <?php if (empty($orders)): ?>

        <div class="page-card">
            <div class="empty-box">
                <h3>No orders found</h3>
                <p>You haven't placed any orders yet.</p>
                <a href="products.php" class="btn">Start Shopping</a>
            </div>
        </div>

    <?php else: ?>

        <div class="page-card">
            <?php foreach ($orders as $order): ?>
                <div class="order-list-item">
                    <h3>Order # <?= e($order['order_number']) ?></h3>
                    <p><strong>Date:</strong> <?= e($order['created_at']) ?></p>
                    <p>
                        <strong>Status:</strong>
                        <span class="order-status <?= e($order['status']) ?>">
                            <?= e($order['status']) ?>
                        </span>
                    </p>
                    <p><strong>Subtotal:</strong> ₹<?= number_format($order['subtotal'], 2) ?></p>
                    <p><strong>Delivery:</strong> ₹<?= number_format($order['delivery_fee'], 2) ?></p>
                    <p><strong>Total:</strong> ₹<?= number_format($order['total'], 2) ?></p>

                    <a href="order-details.php?id=<?= (int)$order['id'] ?>">
                        View Order Details →
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="btn-row">
            <a href="products.php" class="btn">Continue Shopping</a>
        </div>

    <?php endif; ?>

</section>

</body>
</html>
