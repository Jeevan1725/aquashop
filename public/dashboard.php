<?php

require_once __DIR__ . '/../app/services/Auth.php';
require_once __DIR__.'/../app/views/navbar.php';

if(!Auth::check())
{
    header("Location: login.php");
    exit;
}


$user = Auth::user();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard | AquaShop</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<?php require_once __DIR__ . '/../app/views/navbar.php'; ?>

<main class="dashboard-shell">
    <section class="dashboard-hero">
        <div class="dashboard-copy">
            <span class="eyebrow small">CUSTOMER HUB</span>
            <h1>Welcome back, <?= htmlspecialchars($user['name']) ?>.</h1>
            <p>
                Keep your aquarium plans organized, track recent orders, and discover new essentials for your next setup.
            </p>
            <div class="hero-actions">
                <a href="shop.php" class="btn">Continue shopping</a>
                <a href="orders.php" class="btn btn-secondary">View orders</a>
            </div>
        </div>

        <div class="dashboard-badge" aria-label="Customer summary">
            <div class="badge-icon">🐠</div>
            <span>Member status</span>
            <strong><?= htmlspecialchars(strtoupper($user['role'])) ?></strong>
        </div>
    </section>

    <section class="dashboard-stats" aria-label="Summary cards">
        <article class="dashboard-stat">
            <span class="stat-label">Account</span>
            <strong>Verified</strong>
            <small>Your profile is secure and active</small>
        </article>

        <article class="dashboard-stat">
            <span class="stat-label">Delivery</span>
            <strong>Fast</strong>
            <small>Track the next shipment with ease</small>
        </article>

        <article class="dashboard-stat">
            <span class="stat-label">Support</span>
            <strong>Priority</strong>
            <small>Reach help anytime for your setup</small>
        </article>
    </section>

    <section class="dashboard-grid">
        <article class="dashboard-card wide">
            <div class="card-header">
                <div>
                    <span class="eyebrow small">PROFILE</span>
                    <h2>Account details</h2>
                </div>
            </div>

            <div class="dashboard-list">
                <div class="list-item">
                    <span class="list-label">Full name</span>
                    <strong><?= htmlspecialchars($user['name']) ?></strong>
                </div>

                <div class="list-item">
                    <span class="list-label">Email</span>
                    <strong><?= htmlspecialchars($user['email']) ?></strong>
                </div>

                <div class="list-item">
                    <span class="list-label">Account role</span>
                    <strong><?= htmlspecialchars($user['role']) ?></strong>
                </div>
            </div>
        </article>

        <aside class="dashboard-card">
            <div class="card-header">
                <div>
                    <span class="eyebrow small">QUICK ACCESS</span>
                    <h2>Navigate</h2>
                </div>
            </div>

            <div class="dashboard-actions">
                <a href="shop.php" class="action-link">🐟 Browse products</a>
                <a href="orders.php" class="action-link">📦 My orders</a>
                <a href="cart.php" class="action-link">🛒 My cart</a>
                <a href="wishlist.php" class="action-link">❤️ Wishlist</a>
                <a href="profile.php" class="action-link">👤 Profile</a>
            </div>
        </aside>
    </section>

    <section class="dashboard-grid secondary">
        <article class="dashboard-card">
            <div class="card-header">
                <div>
                    <span class="eyebrow small">SHOPPING</span>
                    <h2>What to do next</h2>
                </div>
            </div>

            <div class="dashboard-list compact">
                <div class="list-item">
                    <span class="list-label">Fresh inventory</span>
                    <strong>Explore new arrivals</strong>
                </div>
                <div class="list-item">
                    <span class="list-label">Tank setup</span>
                    <strong>Pick aquariums and equipment</strong>
                </div>
                <div class="list-item">
                    <span class="list-label">Care routine</span>
                    <strong>Buy food and filters for daily care</strong>
                </div>
            </div>
        </article>

        <article class="dashboard-card">
            <div class="card-header">
                <div>
                    <span class="eyebrow small">SUPPORT</span>
                    <h2>Need help?</h2>
                </div>
            </div>

            <div class="dashboard-list compact">
                <div class="list-item">
                    <span class="list-label">Shipping updates</span>
                    <strong>Fast dispatch & tracking</strong>
                </div>
                <div class="list-item">
                    <span class="list-label">Customer care</span>
                    <strong>Helpful guidance for every tank</strong>
                </div>
                <div class="list-item">
                    <span class="list-label">Returns</span>
                    <strong>Easy assistance for order issues</strong>
                </div>
            </div>
        </article>
    </section>
</main>

<footer>
    <p>© 2026 AquaShop. Premium aquarium care for every hobbyist.</p>
</footer>

</body>
</html>