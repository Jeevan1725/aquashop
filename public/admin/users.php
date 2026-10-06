<?php

session_start();

require_once __DIR__ . '/../../app/services/AdminAuth.php';

AdminAuth::check();

require_once __DIR__ . '/../../app/bootstrap.php';

$stmt = $db->query("
    SELECT
        u.id,
        u.name,
        u.email,
        u.status,
        u.created_at,
        r.name AS role
    FROM users u
    INNER JOIN roles r ON u.role_id = r.id
    ORDER BY u.id DESC
");

$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Users | AquaShop Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php require_once __DIR__ . "/../../app/views/navbar.php"; ?>

    <main class="admin-shell">
        <div class="admin-toolbar">
            <div>
                <span class="eyebrow small">USERS</span>
                <h1>Manage users</h1>
            </div>
            <a class="btn" href="index.php">Back dashboard</a>
        </div>

        <div class="dashboard-card admin-panel recent-panel">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><strong><?= htmlspecialchars($user['name']) ?></strong></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td>
                                <?php if($user['status']=="active"): ?>
                                    <span class="status-badge success">Active</span>
                                <?php else: ?>
                                    <span class="status-badge danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($user['created_at']) ?></td>
                            <td>
                                <a class="mini-btn" href="user-edit.php?id=<?= $user['id'] ?>">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
