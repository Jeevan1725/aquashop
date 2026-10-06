<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/bootstrap.php';

$userId = (int)$_SESSION['user']['id'];

$stmt = $db->prepare("
    SELECT id, name, email, phone, created_at
    FROM users
    WHERE id = ?
    LIMIT 1
");
$stmt->execute([$userId]);
$profile = $stmt->fetch();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile | AquaShop</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require_once __DIR__ . '/../app/views/navbar.php'; ?>

<section class="page-section">

    <div class="page-header">
        <span class="eyebrow small">MY ACCOUNT</span>
        <h1>Profile Overview</h1>
        <p>Your account information and quick actions.</p>
    </div>

    <div class="profile-layout" style="display:grid;grid-template-columns:1.4fr 1fr;gap:24px;">

        <div class="page-card">
            <h2>Account Details</h2>
            <div class="list-item">
                <span class="list-label">Name</span>
                <strong><?= e($profile['name']) ?></strong>
            </div>
            <div class="list-item">
                <span class="list-label">Email</span>
                <strong><?= e($profile['email']) ?></strong>
            </div>
            <div class="list-item">
                <span class="list-label">Phone</span>
                <strong><?= e($profile['phone'] ?? 'Not added') ?></strong>
            </div>
            <div class="list-item">
                <span class="list-label">Member Since</span>
                <strong><?= e($profile['created_at']) ?></strong>
            </div>
        </div>

        <div class="page-card">
            <h2>Quick Actions</h2>
            <div class="dashboard-actions">
                <a href="orders.php" class="action-link">📦 My Orders</a>
                <a href="profile-edit.php" class="action-link">✏️ Edit Profile</a>
                <a href="change-password.php" class="action-link">🔐 Change Password</a>
                <a href="addresses.php" class="action-link">📍 Manage Addresses</a>
                <a href="wishlist.php" class="action-link">❤️ Wishlist</a>
                <a href="logout.php" class="action-link">🚪 Logout</a>
            </div>
        </div>

    </div>

</section>

</body>
</html>
