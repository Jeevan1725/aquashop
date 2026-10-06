<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/bootstrap.php';

$userId = (int)$_SESSION['user']['id'];

$stmt = $db->prepare("SELECT name, email, phone FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$userId]);
$user = $stmt->fetch();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);

    $update = $db->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
    $update->execute([$name, $phone, $userId]);

    $_SESSION['user']['name'] = $name;
    $message = "Profile updated successfully.";

    // Refresh displayed values
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile | AquaShop</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require_once __DIR__ . '/../app/views/navbar.php'; ?>

<section class="page-section">

    <div class="page-header">
        <span class="eyebrow small">MY ACCOUNT</span>
        <h1>Edit Profile</h1>
        <p>Update your name and contact details.</p>
    </div>

    <div class="page-card">
        <?php if ($message): ?>
            <div class="auth-success"><?= e($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name"
                   value="<?= e($user['name']) ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email"
                   value="<?= e($user['email']) ?>" disabled>

            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone"
                   value="<?= e($user['phone'] ?? '') ?>">

            <button type="submit" class="btn">Update Profile</button>
        </form>

        <div class="btn-row" style="margin-top:20px;">
            <a href="profile.php" class="btn-secondary btn">← Back to Profile</a>
        </div>
    </div>

</section>

</body>
</html>
