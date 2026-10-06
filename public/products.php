<?php

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/Product.php';
require_once __DIR__ . '/../app/models/Category.php';
require_once __DIR__.'/../app/views/navbar.php';

$productModel = new Product($db);
$categoryModel = new Category($db);

$search = $_GET['search'] ?? null;

$category = isset($_GET['category'])
    ? (int)$_GET['category']
    : null;

$products = $productModel->getAll($search, $category);
$categories = $categoryModel->getAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AquaShop Products</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<section class="products-page">
    <div class="shop-header">
        <div>
            <span class="eyebrow small">SHOP COLLECTION</span>
            <h1>Aquarium products</h1>
        </div>
        <p>Curated essentials for healthy fish, vibrant tanks, and effortless care.</p>
    </div>

    <!-- DOM XSS demo target: JS will write into this element -->
    <div id="highlight-box"
         style="margin-bottom:20px;padding:14px;background:#fffbe6;border-left:4px solid #f0c419;border-radius:6px;display:none;">
        <strong>You are looking for:</strong>
        <span id="highlight-output"></span>
    </div>

    <form method="GET" class="shop-filters">
        <input
            type="text"
            name="search"
            placeholder="Search products or fish..."
            value="<?= e($search ?? '') ?>"
        >

        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>" <?= ($category == $cat['id']) ? 'selected' : '' ?>>
                    <?= e($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Search</button>
    </form>

    <div class="product-grid">
        <?php if (empty($products)): ?>
            <div class="empty-state">
                <h3>No products found.</h3>
                <p>Try a different category or search term to discover more aquarium essentials.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($products as $product): ?>
            <article class="product-card">
                <div class="product-image">
                    <?php if (!empty($product['image'])): ?>
                        <img
                            src="uploads/products/<?= e($product['image']) ?>"
                            alt="<?= e($product['name']) ?>"
                        >
                    <?php else: ?>
                        🐟
                    <?php endif; ?>
                </div>

                <div class="product-info">
                    <span class="category"><?= e($product['category']) ?></span>
                    <h3><?= e($product['name']) ?></h3>
                    <p><?= e($product['description']) ?></p>

                    <div class="product-bottom">
                        <strong>₹<?= number_format((float)$product['price'], 2) ?></strong>
                        <a href="product.php?slug=<?= urlencode($product['slug']) ?>">View Details</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php
/*
|--------------------------------------------------------------------------
| DOM-based XSS demo
|--------------------------------------------------------------------------
|
| The JS below reads ?highlight= from the URL and writes it into
| #highlight-output using innerHTML (vulnerable) or textContent (secure).
|
| SECURE mode:     textContent — payload shown as text, no execution
| VULNERABLE mode: innerHTML  — payload executes
|
*/
$mode = is_secure() ? 'secure' : 'vulnerable';
?>
<script>
(function () {
    var params    = new URLSearchParams(window.location.search);
    var value     = params.get('highlight');
    var box       = document.getElementById('highlight-box');
    var output    = document.getElementById('highlight-output');
    var mode      = <?= json_encode($mode) ?>;

    if (!value) { return; }

    box.style.display = 'block';

    if (mode === 'secure') {
        // SECURE: textContent cannot be parsed as HTML
        output.textContent = value;
    } else {
        // VULNERABLE: innerHTML parses payload as HTML → script executes
        output.innerHTML = value;
    }
})();
</script>

</body>
</html>
