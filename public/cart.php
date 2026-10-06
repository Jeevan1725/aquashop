<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/Cart.php';
require_once __DIR__.'/../app/views/navbar.php';

$cartModel = new Cart($db);

$cartId = $cartModel->getOrCreateCart(
    (int)$_SESSION['user']['id']
);


/*
|--------------------------------------------------------------------------
| Handle Cart Actions
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $itemId = (int)($_POST['item_id'] ?? 0);

    if ($itemId <= 0) {
        die("Invalid cart item.");
    }


    /*
    |--------------------------------------------------------------------------
    | Update Quantity
    |--------------------------------------------------------------------------
    */

    if (isset($_POST['update'])) {

        $quantity = (int)($_POST['quantity'] ?? 1);

        $cartModel->updateQuantity(
            $itemId,
            $quantity
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Remove Item
    |--------------------------------------------------------------------------
    */

    if (isset($_POST['remove'])) {

        $cartModel->removeItem(
            $itemId
        );
    }


    header("Location: cart.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Get Cart Items
|--------------------------------------------------------------------------
*/

$items = $cartModel->getItems($cartId);

$total = 0;

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Cart | AquaShop</title>

    <link rel="stylesheet" href="css/style.css">

</head>


<body>




<section class="products-page">

    <h1>
        Your Cart 🛒
    </h1>


    <?php if (empty($items)): ?>

        <h2>
            Your cart is empty.
        </h2>

        <br>

        <a href="products.php" class="btn">
            Continue Shopping
        </a>

    <?php else: ?>


        <?php foreach ($items as $item): ?>

            <div class="product-card">

                <div class="product-info">

                    <h3>
                        <?= htmlspecialchars(
                            $item['name'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h3>


                    <p>
                        ₹<?= number_format(
                            (float)$item['price'],
                            2
                        ) ?>
                    </p>


                    <form
                        method="POST"
                        action="cart.php"
                    >

                        <input
                            type="hidden"
                            name="item_id"
                            value="<?= (int)$item['cart_item_id'] ?>"
                        >


                        <label>
                            Quantity
                        </label>


                        <input
                            type="number"
                            name="quantity"
                            value="<?= (int)$item['quantity'] ?>"
                            min="1"
                            max="<?= (int)$item['stock'] ?>"
                        >


                        <button
                            type="submit"
                            name="update"
                        >
                            Update
                        </button>


                        <button
                            type="submit"
                            name="remove"
                            value="1"
                        >
                            Remove
                        </button>

                    </form>


                    <p>

                        Item Total:

                        ₹<?= number_format(
                            (float)$item['price'] *
                            (int)$item['quantity'],
                            2
                        ) ?>

                    </p>

                </div>

            </div>


            <?php

            $total +=
                (float)$item['price'] *
                (int)$item['quantity'];

            ?>

        <?php endforeach; ?>


        <h2>

            Total:

            ₹<?= number_format($total, 2) ?>

        </h2>


        <br>


        <a href="products.php">
            Continue Shopping
        </a>


        <br><br>


        <a href="checkout.php">
            Proceed to Checkout
        </a>

    <?php endif; ?>


</section>


</body>

</html>