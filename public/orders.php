<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/Order.php';

$orderModel = new Order($db);

$userId = (int) $_SESSION['user']['id'];

$orders = $orderModel->getOrdersByUser($userId);

?>

<!DOCTYPE html>

<html>

<head>

<title>My Orders | AquaShop</title>

<link rel="stylesheet" href="css/style.css">

</head>

<body>

<h1>My Orders</h1>

<p>
Welcome,
<?= htmlspecialchars($_SESSION['user']['name']) ?>
</p>

<hr>

<?php if (empty($orders)): ?>

    <h2>No orders found.</h2>

    <a href="products.php">
        Start Shopping
    </a>

<?php else: ?>

    <?php foreach ($orders as $order): ?>

        <div>

            <h2>
                Order #
                <?= htmlspecialchars($order['order_number']) ?>
            </h2>

            <p>
                Date:
                <?= htmlspecialchars($order['created_at']) ?>
            </p>

            <p>
                Status:
                <strong>
                    <?= htmlspecialchars($order['status']) ?>
                </strong>
            </p>

            <p>
                Subtotal:
                ₹<?= number_format($order['subtotal'], 2) ?>
            </p>

            <p>
                Delivery:
                ₹<?= number_format($order['delivery_fee'], 2) ?>
            </p>

            <p>
                Total:
                <strong>
                    ₹<?= number_format($order['total'], 2) ?>
                </strong>
            </p>

            <a href="order-details.php?id=<?= (int) $order['id'] ?>">
                View Order
            </a>

        </div>

        <hr>

    <?php endforeach; ?>

<?php endif; ?>

<br>

<a href="products.php">
    Continue Shopping
</a>

</body>

</html>
