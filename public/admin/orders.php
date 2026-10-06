<?php

require_once __DIR__ . '/../../app/services/AdminAuth.php';

AdminAuth::check();

require_once __DIR__ . '/../../app/bootstrap.php';

$stmt = $db->query("
SELECT
    o.id,
    o.order_number,
    o.total,
    o.status,
    o.created_at,
    u.name AS customer,
    u.email
FROM orders o
INNER JOIN users u ON o.user_id = u.id
ORDER BY o.created_at DESC
");

$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php require_once __DIR__ . "/../../app/views/navbar.php"; ?>

    <section class="admin-shell">
        <div class="admin-toolbar">
            <div>
                <span class="eyebrow small">ORDERS</span>
                <h1>Manage orders</h1>
            </div>
            <a class="btn" href="index.php">← Back dashboard</a>
        </div>

        <div class="admin-panel recent-panel">
            <table class="admin-table">
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>

                <?php foreach($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['order_number']) ?></td>
                        <td><?= htmlspecialchars($order['customer']) ?></td>
                        <td><?= htmlspecialchars($order['email']) ?></td>
                        <td>₹<?= number_format($order['total'],2) ?></td>
                        <td>
                            <?php if($order['status']=="pending"): ?>
                                <span class="status-badge warning">🟡 Pending</span>
                            <?php elseif($order['status']=="processing"): ?>
                                <span class="status-badge info">🔵 Processing</span>
                            <?php elseif($order['status']=="confirmed"): ?>
                                <span class="status-badge success">🟢 Confirmed</span>
                            <?php elseif($order['status']=="delivered"): ?>
                                <span class="status-badge success">✅ Delivered</span>
                            <?php else: ?>
                                <span class="status-badge"><?= htmlspecialchars($order['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($order['created_at']) ?></td>
                        <td><a class="mini-btn" href="order-view.php?id=<?= $order['id'] ?>">👁 View</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </section>
</body>
</html>
