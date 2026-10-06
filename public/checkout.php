<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/Cart.php';
require_once __DIR__ . '/../app/models/Address.php';
require_once __DIR__ . '/../app/views/navbar.php';

$userId = (int)$_SESSION['user']['id'];
$cartModel = new Cart($db);
$addressModel = new Address($db);

$cartId = $cartModel->getOrCreateCart($userId);
$items = $cartModel->getItems($cartId);

if (empty($items)) {
    die("Your cart is empty.");
}

$addresses = $addressModel->getByUser($userId);

if (empty($addresses)) {
    header("Location: addresses.php");
    exit;
}

$subtotal = 0;
foreach ($items as $item) {
    $subtotal += (float)$item['price'] * (int)$item['quantity'];
}

$delivery = 50;
$total = $subtotal + $delivery;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | AquaShop</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<section class="checkout-page">
    <div class="checkout-header">
        <h1>Checkout</h1>
        <p>Complete your order with secure delivery and quick shipping.</p>
    </div>

    <div class="checkout-layout">
        <div class="checkout-stack">
            <div class="checkout-panel">
                <h2>Delivery Address</h2>
                <div class="address-list">
                    <?php foreach ($addresses as $index => $address): ?>
                        <label class="address-option">
                            <input type="radio" name="selected_address" value="<?= (int)$address['id'] ?>" form="order-form" <?= $index === 0 ? 'checked' : '' ?> required>
                            <strong><?= htmlspecialchars($address['full_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <p>
                                <strong>Phone:</strong> <?= htmlspecialchars($address['phone'], ENT_QUOTES, 'UTF-8') ?><br>
                                <?= htmlspecialchars($address['address_line1'], ENT_QUOTES, 'UTF-8') ?><br>
                                <?php if (!empty($address['address_line2'])): ?>
                                    <?= htmlspecialchars($address['address_line2'], ENT_QUOTES, 'UTF-8') ?><br>
                                <?php endif; ?>
                                <?= htmlspecialchars($address['city'], ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($address['state'], ENT_QUOTES, 'UTF-8') ?><br>
                                <?= htmlspecialchars($address['postal_code'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </label>
                    <?php endforeach; ?>
                </div>
                <a href="address-add.php" class="secondary-link">+ Add New Address</a>
            </div>

            <div class="checkout-panel">
                <h2>Order Summary</h2>
                <div class="order-list">
                    <?php foreach ($items as $item): ?>
                        <div class="order-item">
                            <div>
                                <strong><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></strong><br>
                                <small>Qty: <?= (int)$item['quantity'] ?></small>
                            </div>
                            <span>₹<?= number_format((float)$item['price'] * (int)$item['quantity'], 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <aside class="summary-card">
            <h2>Payment Summary</h2>
            <div class="summary-row">
                <span>Subtotal</span>
                <strong>₹<?= number_format($subtotal, 2) ?></strong>
            </div>
            <div class="summary-row">
                <span>Delivery</span>
                <strong>₹<?= number_format($delivery, 2) ?></strong>
            </div>
            <div class="summary-row summary-total">
                <span>Total</span>
                <span>₹<?= number_format($total, 2) ?></span>
            </div>

            <form method="POST" action="place-order.php" id="order-form">
                <input type="hidden" name="address_id" id="address_id" value="<?= (int)$addresses[0]['id'] ?>">
                <button type="submit" class="btn place-order-button">Place Order</button>
            </form>
        </aside>
    </div>
</section>

<script>
    const addressRadios = document.querySelectorAll('input[name="selected_address"]');
    const addressInput = document.getElementById('address_id');

    addressRadios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            addressInput.value = this.value;
        });
    });
</script>

</body>
</html>
