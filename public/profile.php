<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__.'/../app/views/navbar.php';

$user = $_SESSION['user'];


// Get latest user details

$stmt = $db->prepare("
    SELECT
        id,
        name,
        email,
        phone,
        created_at
    FROM users
    WHERE id = ?
    LIMIT 1
");


$stmt->execute([
    $user['id']
]);


$profile = $stmt->fetch();


?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">


<title>
My Profile | AquaShop
</title>


<link rel="stylesheet" href="css/style.css">


</head>


<body>

<?php require_once __DIR__.'/../app/views/navbar.php'; ?>

<section class="profile-page">
    <div class="profile-header">
        <div>
            <span class="eyebrow small">MY ACCOUNT</span>
            <h1>Profile overview</h1>
        </div>
    </div>

    <div class="profile-layout">
        <article class="profile-panel profile-main">
            <div class="card-header">
                <div>
                    <span class="eyebrow small">PROFILE</span>
                    <h2>Account details</h2>
                </div>
            </div>

            <div class="profile-list">
                <div class="list-item">
                    <span class="list-label">Name</span>
                    <strong><?= htmlspecialchars($profile['name']) ?></strong>
                </div>

                <div class="list-item">
                    <span class="list-label">Email</span>
                    <strong><?= htmlspecialchars($profile['email']) ?></strong>
                </div>

                <div class="list-item">
                    <span class="list-label">Phone</span>
                    <strong><?= htmlspecialchars($profile['phone'] ?? 'Not added') ?></strong>
                </div>

                <div class="list-item">
                    <span class="list-label">Member since</span>
                    <strong><?= htmlspecialchars($profile['created_at']) ?></strong>
                </div>
            </div>
        </article>

        <aside class="profile-panel profile-actions">
            <div class="card-header">
                <div>
                    <span class="eyebrow small">MANAGE</span>
                    <h2>Quick actions</h2>
                </div>
            </div>

            <div class="dashboard-actions">
                <a href="orders.php" class="action-link">📦 My orders</a>
                <a href="profile-edit.php" class="action-link">✏️ Edit profile</a>
                <a href="change-password.php" class="action-link">🔐 Change password</a>
                <a href="addresses.php" class="action-link">📍 Manage addresses</a>
                <a href="wishlist.php" class="action-link">❤️ Wishlist</a>
                <a href="logout.php" class="action-link">🚪 Logout</a>
            </div>
        </aside>
    </div>
</section>

</body>

</html>
