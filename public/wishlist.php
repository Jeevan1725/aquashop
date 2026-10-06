<?php

session_start();

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/Wishlist.php';
require_once __DIR__.'/../app/views/navbar.php';

// User must be logged in

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}


$userId = (int)$_SESSION['user']['id'];

$wishlistModel = new Wishlist($db);

$wishlistItems = $wishlistModel->getByUser($userId);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Wishlist | AquaShop</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

    <style>

        .wishlist-page {
            padding: 60px 7%;
        }

        .wishlist-page h1 {
            text-align: center;
            margin-bottom: 40px;
        }

        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
        }

        .wishlist-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        }

        .wishlist-image {
            height: 220px;
            background: #e7f7fa;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            font-size: 70px;
        }

        .wishlist-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .wishlist-info {
            padding: 20px;
        }

        .wishlist-info h3 {
            margin: 8px 0;
        }

        .wishlist-category {
            font-size: 13px;
            color: #087e8b;
            font-weight: bold;
        }

        .wishlist-price {
            font-size: 20px;
            font-weight: bold;
            margin: 15px 0;
        }

        .wishlist-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .wishlist-actions a,
        .wishlist-actions button {
            padding: 9px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .wishlist-cart {
            background: #087e8b;
            color: white;
        }

        .wishlist-remove {
            background: #dc3545;
            color: white;
        }

        .empty-wishlist {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 12px;
        }

        @media (max-width: 900px) {

            .wishlist-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 600px) {

            .wishlist-grid {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>


<body>



<!-- =========================
     WISHLIST
========================= -->

<section class="wishlist-page">

    <h1>
        ❤️ My Wishlist
    </h1>


    <?php if (empty($wishlistItems)): ?>

        <div class="empty-wishlist">

            <h2>
                Your wishlist is empty
            </h2>

            <p>
                Browse our aquarium products and
                add your favorites here.
            </p>

            <br>

            <a
                href="products.php"
                class="btn"
            >
                Browse Products
            </a>

        </div>

    <?php else: ?>


        <div class="wishlist-grid">


            <?php foreach ($wishlistItems as $product): ?>

                <div class="wishlist-card">


                    <!-- IMAGE -->

                    <div class="wishlist-image">

                        <?php if (!empty($product['image'])): ?>

                            <img
                                src="uploads/products/<?= htmlspecialchars(
                                    $product['image'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                alt="<?= htmlspecialchars(
                                    $product['name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >

                        <?php else: ?>

                            🐟

                        <?php endif; ?>

                    </div>


                    <!-- INFORMATION -->

                    <div class="wishlist-info">


                        <span class="wishlist-category">

                            <?= htmlspecialchars(
                                $product['category'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </span>


                        <h3>

                            <?= htmlspecialchars(
                                $product['name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </h3>


                        <div class="wishlist-price">

                            ₹<?= number_format(
                                (float)$product['price'],
                                2
                            ) ?>

                        </div>


                        <div class="wishlist-actions">


                            <a
                                href="product.php?slug=<?= urlencode(
                                    $product['slug']
                                ) ?>"
                                class="wishlist-cart"
                            >
                                View Product
                            </a>


                            <form
                                method="POST"
                                action="wishlist-remove.php"
                            >

                                <input
                                    type="hidden"
                                    name="product_id"
                                    value="<?= (int)$product['id'] ?>"
                                >

                                <button
                                    type="submit"
                                    class="wishlist-remove"
                                >
                                    Remove
                                </button>

                            </form>


                        </div>


                    </div>


                </div>

            <?php endforeach; ?>


        </div>

    <?php endif; ?>


</section>


</body>

</html>
