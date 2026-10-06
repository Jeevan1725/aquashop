<?php

session_start();

require_once __DIR__ . '/../../app/services/AdminAuth.php';

AdminAuth::check();

require_once __DIR__ . '/../../app/bootstrap.php';
require_once __DIR__ . '/../../app/services/CSRF.php';

$stmt = $db->query("
    SELECT
        p.id,
        p.name,
        p.price,
        p.stock,
        p.status,
        c.name AS category
    FROM products p
    INNER JOIN categories c ON p.category_id = c.id
    ORDER BY p.id DESC
");

$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Products | AquaShop Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php require_once __DIR__ . "/../../app/views/navbar.php"; ?>

    <section class="admin-shell">
        <div class="admin-toolbar">
            <div>
                <span class="eyebrow small">PRODUCTS</span>
                <h1>Manage products</h1>
            </div>
            <a class="btn" href="add-product.php">➕ Add product</a>
        </div>

        <div class="admin-panel recent-panel">
            <table class="admin-table">
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

                <?php foreach($products as $product): ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td>🐟 <?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['category']) ?></td>
                        <td>₹<?= number_format($product['price'],2) ?></td>
                        <td><?= $product['stock'] ?></td>
                        <td>
                            <?php if($product['status']=="active"): ?>
                                <span class="status-badge success">Active</span>
                            <?php else: ?>
                                <span class="status-badge danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a class="mini-btn" href="edit-product.php?id=<?= $product['id'] ?>">✏️ Edit</a>
                                <form method="POST" action="delete-product.php" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= CSRF::generate() ?>">
                                    <button class="mini-btn danger" type="submit" onclick="return confirm('Delete this product?');">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </section>
</body>
</html>
