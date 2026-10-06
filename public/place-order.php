<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/Cart.php';
require_once __DIR__ . '/../app/models/Order.php';
require_once __DIR__ . '/../app/models/Address.php';
require_once __DIR__ . '/../app/models/Payment.php';

$userId = (int) $_SESSION['user']['id'];

$addressId = (int) ($_POST['address_id'] ?? 0);

if ($addressId <= 0) {
    die("Invalid address.");
}

/*
 * Verify that the selected address
 * belongs to the logged-in user.
 */
$addressModel = new Address($db);

$address = $addressModel->getById(
    $addressId,
    $userId
);

if (!$address) {
    die("Invalid delivery address.");
}


/*
 * Get user's cart.
 */
$cartModel = new Cart($db);

$cartId = $cartModel->getOrCreateCart(
    $userId
);

$items = $cartModel->getItems(
    $cartId
);

if (empty($items)) {
    die("Your cart is empty.");
}


try {

    /*
     * Create the order.
     */
    $orderModel = new Order($db);

    $orderId = $orderModel->createOrder(
        $userId,
        $addressId,
        $items
    );


    /*
     * Get the newly created order.
     * We use the server-side total from
     * the database.
     */
    $orderStmt = $db->prepare("
        SELECT id, total
        FROM orders
        WHERE id = ?
        AND user_id = ?
        LIMIT 1
    ");

    $orderStmt->execute([
        $orderId,
        $userId
    ]);

    $order = $orderStmt->fetch();

    if (!$order) {
        throw new Exception("Order not found.");
    }


    /*
     * Create payment record.
     *
     * This is currently sandbox mode.
     * No real payment is performed yet.
     */
    $paymentModel = new Payment($db);

    $paymentModel->create(
        (int) $order['id'],
        'sandbox',
        (float) $order['total']
    );


    /*
     * Clear the cart only after
     * the order was successfully created.
     */
    $stmt = $db->prepare("
        DELETE FROM cart_items
        WHERE cart_id = ?
    ");

    $stmt->execute([
        $cartId
    ]);


    /*
     * Redirect to order success page.
     */
    header(
        "Location: order-success.php?id=" .
        $orderId
    );

    exit;


} catch (Throwable $e) {

    /*
     * Temporary development error.
     * Later we will replace this with
     * server-side logging + generic error.
     */
    die(
        "Order creation failed: " .
        htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        )
    );
}