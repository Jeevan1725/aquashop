<?php

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/Product.php';

$productModel = new Product($db);
$products = $productModel->getFeaturedProducts();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AquaShop | Aquarium & Ornamental Fish</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<?php require_once __DIR__ . "/../app/views/navbar.php"; ?>


<section class="hero">

    <div class="hero-content">

        <div class="eyebrow">AQUARIUM ESSENTIALS</div>

        <h1>
            Bring Your Aquarium to Life
        </h1>

        <p>
            Discover premium ornamental fish, aquariums, aquatic plants,
            equipment and nutrition curated for healthier, happier tanks.
        </p>

        <div class="hero-actions">
            <a href="products.php" class="btn">
                Shop Now
            </a>
            <a href="products.php?category=10" class="btn btn-secondary">
                View Aquariums
            </a>
        </div>

    </div>

    <div class="hero-visual" aria-hidden="true">
        <div class="showcase-card">
            <div class="showcase-header">
                <span class="mini-tag">New Arrival</span>
                <span class="rating">★ 4.9</span>
            </div>

            <div class="aquarium-scene">
                <div class="tank-glass">
                    <div class="water"></div>
                    <div class="fish fish-one"></div>
                    <div class="fish fish-two"></div>
                    <div class="plant plant-one"></div>
                    <div class="plant plant-two"></div>
                    <div class="plant plant-three"></div>
                </div>
            </div>

            <div class="showcase-footer">
                <div>
                    <small>Complete reef setup</small>
                    <strong>Premium Aqua Kit</strong>
                </div>
                <span>₹4,499</span>
            </div>
        </div>

        <div class="floating-badge">
            <strong>98%</strong>
            <span>Healthy tank score</span>
        </div>
    </div>

</section>

<section class="feature-strip">
    <div class="stat-box">
        <strong>1200+</strong>
        <span>Happy aquarists</span>
    </div>
    <div class="stat-box">
        <strong>4.9/5</strong>
        <span>Customer rating</span>
    </div>
    <div class="stat-box">
        <strong>48h</strong>
        <span>Fast dispatch</span>
    </div>
</section>

<section class="categories">

<div class="section-head">
    <div>
        <div class="eyebrow small">SHOP COLLECTIONS</div>
        <h2>Explore the essentials</h2>
    </div>
</div>

<div class="category-grid">


<a href="products.php?category=7" class="category-card">
    <span>01</span>
    <h3>Freshwater Fish</h3>
</a>

<a href="products.php?category=8" class="category-card">
    <span>02</span>
    <h3>Marine Fish</h3>
</a>

<a href="products.php?category=9" class="category-card">
    <span>03</span>
    <h3>Aquatic Plants</h3>
</a>

<a href="products.php?category=10" class="category-card">
    <span>04</span>
    <h3>Aquariums</h3>
</a>

<a href="products.php?category=11" class="category-card">
    <span>05</span>
    <h3>Equipment</h3>
</a>

<a href="products.php?category=12" class="category-card">
    <span>06</span>
    <h3>Fish Food</h3>
</a>


</div>

</section>

<section class="benefits">
    <div class="section-head centered">
        <div class="eyebrow small">WHY CHOOSE US</div>
        <h2>Built for healthier tanks</h2>
    </div>

    <div class="benefit-grid">
        <div class="benefit-card">
            <div class="benefit-icon">01</div>
            <h3>Premium aquatic care</h3>
            <p>Curated products selected for quality, durability, and long-term aquatic health.</p>
        </div>
        <div class="benefit-card">
            <div class="benefit-icon">02</div>
            <h3>Expert recommendations</h3>
            <p>Easy-to-shop categories help hobbyists find the right setup for their tank and species.</p>
        </div>
        <div class="benefit-card">
            <div class="benefit-icon">03</div>
            <h3>Reliable delivery</h3>
            <p>Fast fulfillment and careful handling keep your aquarium essentials protected and ready to use.</p>
        </div>
    </div>
</section>

<section class="products">

    <div class="section-head split">
        <div>
            <div class="eyebrow small">FEATURED PICKS</div>
            <h2>Best sellers</h2>
        </div>
        <a href="products.php" class="text-link">Browse all</a>
    </div>

    <div class="product-grid">

        <?php foreach ($products as $product): ?>

            <div class="product-card">

                            <div class="product-image">

                <?php if (!empty($product['image'])): ?>

                    <img
                        src="uploads/products/<?= e($product['image']) ?>"
                        alt="<?= e($product['name']) ?>"
                    >

                <?php else: ?>

                    🐠

                <?php endif; ?>

            </div>

                <div class="product-info">

                    <span class="category">
                        <?= e($product['category']) ?>
                    </span>

                    <h3>
                        <?= e($product['name']) ?>
                    </h3>

                    <p>
                        <?= e($product['description']) ?>
                    </p>

                    <div class="product-bottom">

                    <strong>
                    ₹<?= number_format((float)$product['price'], 2) ?>
                    </strong>


                    <p>
                    Stock:
                    <?= $product['stock'] ?>
                    </p>


                    <a href="product.php?slug=<?= urlencode($product['slug']) ?>">
                    View Product
                    </a>


                    <form method="POST" action="cart-add.php">

                    <input
                    type="hidden"
                    name="product_id"
                    value="<?= $product['id'] ?>"
                    >


                    <?php if($product['stock'] > 0): ?>

                    <button type="submit">
                    Add to Cart
                    </button>


                    <?php else: ?>

                    <button disabled>
                    Out of Stock
                    </button>


                    <?php endif; ?>


                    </form>


                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</section>


<footer>

    <p>
        © <?= date('Y') ?> AquaShop. All rights reserved.
    </p>

</footer>

</body>
</html>
