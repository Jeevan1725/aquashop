<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/bootstrap.php';

$orderId = (int) ($_GET['id'] ?? 0);

if ($orderId <= 0) {
    die("Invalid order.");
}


$stmt = $db->prepare("
    SELECT
        id,
        order_number,
        subtotal,
        delivery_fee,
        tax,
        discount,
        total,
        status,
        created_at
    FROM orders
    WHERE id = ?
      AND user_id = ?
    LIMIT 1
");

$stmt->execute([
    $orderId,
    $_SESSION['user']['id']
]);

$order = $stmt->fetch();

if (!$order) {
    die("Order not found.");
}

?>


<!DOCTYPE html>

<html>

<head>

<title>
Order Confirmed | AquaShop
</title>

<link rel="stylesheet" href="css/style.css">

</head>


<body>

<h1>
🎉 Order Placed Successfully!
</h1>


<h2>
Order Number:
<?= htmlspecialchars($order['order_number']) ?>
</h2>


<p>
Status:
<?= htmlspecialchars($order['status']) ?>
</p>


<hr>


<p>
Subtotal:
₹<?= number_format($order['subtotal'], 2) ?>
</p>


<p>
Delivery:
₹<?= number_format($order['delivery_fee'], 2) ?>
</p>


<p>
Tax:
₹<?= number_format($order['tax'], 2) ?>
</p>


<p>
Total:
₹<?= number_format($order['total'], 2) ?>
</p>


<br>


<a href="products.php">
Continue Shopping
</a>

</body>

</html>
