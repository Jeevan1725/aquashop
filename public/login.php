<?php

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/services/Auth.php';

$userModel = new User($db);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    /*
    |--------------------------------------------------------------------------
    | VULNERABLE branch
    |--------------------------------------------------------------------------
    */
    if (is_vulnerable()) {

        $user = $userModel->findByEmailVulnerable($email);

        /*
         * VULN: no password hash check — string comparison of plaintext
         *       BUT the SQLi bypass means we often don't even reach it.
         */
        if ($user) {
            Auth::login($user);

            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        }

        $message = "Invalid email or password";

    /*
    |--------------------------------------------------------------------------
    | SECURE branch (unchanged)
    |--------------------------------------------------------------------------
    */
    } else {

        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {

            Auth::login($user);

            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        }

        $message = "Invalid email or password";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | AquaShop</title>
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

        <h1>Welcome back</h1>
        <p class="auth-subtitle">Sign in to continue caring for your aquarium.</p>

        <?php if (is_vulnerable()): ?>
            <div class="auth-error" style="background:#fff3cd;color:#856404;">
                ⚠️ <strong>VULNERABLE MODE</strong> — SQL injection is possible in this field.
            </div>
        <?php else: ?>
            <div class="auth-success">
                🛡️ <strong>SECURE MODE</strong> — prepared statements active.
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="auth-error">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <label for="email">Email</label>
            <input id="email" type="text" name="email" placeholder="Enter email" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Enter password" required>

            <button type="submit" class="btn auth-btn">Login</button>
        </form>

        <p class="auth-link">
            New customer?
            <a href="register.php">Create Account</a>
        </p>
    </div>
</div>

</body>
</html>
