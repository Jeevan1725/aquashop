<?php

session_start();

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/User.php';

$userModel = new User($db);

$message = "";
$messageType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    try {

        $userModel->create(
            $name,
            $email,
            $password,
            $phone
        );

        $message = "Registration successful. You can now login.";
        $messageType = "success";

    } catch (PDOException $e) {

        $message = "Email already exists.";
        $messageType = "error";
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>Create Account | AquaShop</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<?php require_once __DIR__.'/../app/views/navbar.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-brand">
            <span>🐠</span>
            <small>AquaShop</small>
        </div>

        <h1>Create your account</h1>
        <p class="auth-subtitle">Start building the aquarium setup you’ve been dreaming about.</p>

        <?php if ($message): ?>
            <div class="<?= $messageType === 'success' ? 'auth-success' : 'auth-error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <label for="name">Full Name</label>
            <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter your full name"
                required
                autocomplete="name"
            >

            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email"
                required
                autocomplete="email"
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Create a password"
                required
                autocomplete="new-password"
            >

            <label for="phone">Phone</label>
            <input
                type="tel"
                id="phone"
                name="phone"
                placeholder="Enter your phone number"
                autocomplete="tel"
            >

            <button type="submit" class="btn auth-btn">Create Account</button>
        </form>

        <div class="auth-link">
            Already have an account?
            <a href="login.php">Login</a>
        </div>
    </div>
</div>

</body>

</html>