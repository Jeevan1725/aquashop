<?php

session_start();

require_once __DIR__ . '/../../app/services/AdminAuth.php';

AdminAuth::check();

require_once __DIR__ . '/../../app/bootstrap.php';

// Statistics
$totalUsers = $db->query("
    SELECT COUNT(*) FROM users
")->fetchColumn();

$totalProducts = $db->query("
    SELECT COUNT(*)
    FROM products
    WHERE status='active'
")->fetchColumn();

$totalOrders = $db->query("
    SELECT COUNT(*)
    FROM orders
")->fetchColumn();

$totalRevenue = $db->query("
    SELECT COALESCE(SUM(total),0)
    FROM orders
    WHERE status != 'cancelled'
")->fetchColumn();

// Recent Orders
$recentOrders = $db->query("
SELECT
    order_number,
    total,
    status,
    created_at
FROM orders
ORDER BY created_at DESC
LIMIT 5
")->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>AquaShop Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php require_once __DIR__ . "/../../app/views/navbar.php"; ?>

    <section class="admin-shell">
        <div class="admin-hero">
            <div>
                <span class="eyebrow small">ADMIN CONTROL</span>
                <h1>Welcome back, AquaShop Admin</h1>
                <p>Monitor the store, manage orders, and keep your inventory running smoothly.</p>
            </div>

            <div class="admin-badge">
                <span>System status</span>
                <strong>Online</strong>
            </div>
        </div>

        <div class="stats-container">
            <div class="stat-card metric-card">
                <span class="stat-label">Users</span>
                <h3><?= $totalUsers ?></h3>
                <small>Active accounts</small>
            </div>

            <div class="stat-card metric-card">
                <span class="stat-label">Products</span>
                <h3><?= $totalProducts ?></h3>
                <small>Live listings</small>
            </div>

            <div class="stat-card metric-card">
                <span class="stat-label">Orders</span>
                <h3><?= $totalOrders ?></h3>
                <small>Total orders</small>
            </div>

            <div class="stat-card metric-card">
                <span class="stat-label">Revenue</span>
                <h3>₹<?= number_format($totalRevenue,2) ?></h3>
                <small>Gross sales</small>
            </div>
        </div>

        <div class="admin-grid">
            <article class="admin-panel">
                <div class="card-header">
                    <div>
                        <span class="eyebrow small">PROFILE</span>
                        <h2>Administrator profile</h2>
                    </div>
                </div>

                <div class="dashboard-list">
                    <div class="list-item">
                        <span class="list-label">Email</span>
                        <strong>admin@aquashop.com</strong>
                    </div>
                    <div class="list-item">
                        <span class="list-label">Role</span>
                        <strong>Administrator</strong>
                    </div>
                </div>
            </article>

            <article class="admin-panel">
                <div class="card-header">
                    <div>
                        <span class="eyebrow small">ACTIONS</span>
                        <h2>Quick access</h2>
                    </div>
                </div>

                <div class="dashboard-actions">
                    <a href="products.php" class="action-link">🐟 Manage products</a>
                    <a href="orders.php" class="action-link">📦 Manage orders</a>
                    <a href="users.php" class="action-link">👥 Manage users</a>
                </div>
            </article>
        </div>

        <div class="admin-panel recent-panel">
            <div class="card-header">
                <div>
                    <span class="eyebrow small">RECENT</span>
                    <h2>Latest orders</h2>
                </div>
            </div>

            <table class="admin-table">
                <tr>
                    <th>Order</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>

                <?php foreach($recentOrders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['order_number']) ?></td>
                        <td>₹<?= number_format($order['total'],2) ?></td>
                        <td><span class="status-badge"><?= htmlspecialchars($order['status']) ?></span></td>
                        <td><?= htmlspecialchars($order['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </section>
</body>
</html>
