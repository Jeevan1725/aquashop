<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/User.php';

$userModel = new User($db);
$message = "";
$messageType = "error";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $current  = $_POST['current_password'] ?? "";
    $new      = $_POST['new_password'] ?? "";
    $confirm  = $_POST['confirm_password'] ?? "";
    $userId   = (int)$_SESSION['user']['id'];

    $user = $userModel->findById($userId);

    if (!$user) {
        $message = "User not found";
    } elseif (!password_verify($current, $user['password_hash'])) {
        $message = "Current password is incorrect";
    } elseif ($new !== $confirm) {
        $message = "New passwords do not match";
    } elseif (strlen($new) < 6) {
        $message = "Password must be at least 6 characters";
    } else {
        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $userModel->updatePassword($userId, $newHash);
        $message = "Password updated successfully";
        $messageType = "success";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Change Password | AquaShop</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require_once __DIR__ . '/../app/views/navbar.php'; ?>

<section class="page-section">

    <div class="page-header">
        <span class="eyebrow small">MY ACCOUNT</span>
        <h1>Change Password</h1>
        <p>Keep your account secure with a strong password.</p>
    </div>

    <div class="page-card" style="max-width:520px;">

        <?php if ($message): ?>
            <div class="<?= $messageType === 'success' ? 'auth-success' : 'auth-error' ?>">
                <?= e($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" required>

            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password"
                   minlength="6" required>

            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password"
                   minlength="6" required>

            <button type="submit" class="btn">Update Password</button>
        </form>

        <div class="btn-row" style="margin-top:20px;">
            <a href="profile.php" class="btn-secondary btn">← Back to Profile</a>
        </div>
    </div>

</section>

</body>
</html>
