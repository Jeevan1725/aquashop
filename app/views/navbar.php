<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user     = $_SESSION['user'] ?? null;
$isSecure = ($_SESSION['mode'] ?? 'vulnerable') === 'secure';
?>
<header class="navbar">
    <div class="logo">🐠 AquaShop</div>

    <nav>
        <a href="index.php">Home</a>
        <a href="products.php">Shop</a>

        <?php if ($user): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="orders.php">Orders</a>
            <a href="cart.php">Cart 🛒</a>
            <a href="wishlist.php">❤️ Wishlist</a>
            <a href="addresses.php">📍 Addresses</a>
            <a href="profile.php">Profile</a>

            <?php if (($user['role'] ?? '') === 'admin'): ?>
                <a href="admin/index.php">Admin</a>
            <?php endif; ?>

            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>

        <a href="toggle.php"
           class="mode-toggle <?= $isSecure ? 'mode-secure' : 'mode-vuln' ?>"
           title="Click to switch security mode">
            <?= $isSecure ? '🛡️ SECURE' : '⚠️ VULNERABLE' ?>
        </a>
    </nav>
</header>
