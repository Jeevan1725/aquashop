<?php

session_start();

require_once __DIR__ . '/../app/models/Cart.php';
require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/Product.php';

/*
|--------------------------------------------------------------------------
| Image proxy (Path Traversal demo)
|--------------------------------------------------------------------------
|
| SECURE:     resolve realpath() and require that it lives inside uploads/
| VULNERABLE: read any file named in ?img=
|
*/
if (isset($_GET['img'])) {

    $img = $_GET['img'];

    $baseDir = __DIR__ . '/uploads/products/';

    if (is_secure()) {

        // SECURE: canonicalize and ensure the file is inside uploads/
        $real = realpath($baseDir . $img);

        if ($real === false || strpos($real, realpath($baseDir)) !== 0) {
            http_response_code(404);
            die("Image not found");
        }

        $file = $real;

    } else {

        // VULN: no traversal check — ?img=../../../../etc/passwd works
        $file = $baseDir . $img;
    }

    if (!is_file($file)) {
        http_response_code(404);
        die("Image not found");
    }

    // Serve it
    $mime = mime_content_type($file) ?: 'application/octet-stream';
    header("Content-Type: " . $mime);
    header("Content-Length: " . filesize($file));
    readfile($file);
    exit;
}


$productModel = new Product($db);

$slug = $_GET['slug'] ?? null;

if (!$slug) {
    die("Product not found");
}

$product = $productModel->getBySlug($slug);

if (!$product) {
    die("Product not found");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }

    $quantity = (int)($_POST['quantity'] ?? 1);

    if ($quantity < 1) {
        $quantity = 1;
    }

    if ($quantity > (int)$product['stock']) {
        $quantity = (int)$product['stock'];
    }

    if ($quantity <= 0) {
        die("Product is out of stock");
    }

    $cartModel = new Cart($db);

    $cartId = $cartModel->getOrCreateCart(
        $_SESSION['user']['id']
    );

    $cartModel->addItem(
        $cartId,
        $product['id'],
        $quantity
    );

    header("Location: cart.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($product['name']) ?> | AquaShop</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require_once __DIR__ . '/../app/views/navbar.php'; ?>

<section class="product-details">
    <div class="product-large-image">
        <?php if (!empty($product['image'])): ?>
            <img
                src="product.php?img=<?= urlencode($product['image']) ?>"
                alt="<?= e($product['name']) ?>"
            >
        <?php else: ?>
            🐟
        <?php endif; ?>
    </div>

    <div class="product-information">
        <span class="category"><?= e($product['category']) ?></span>

        <h1><?= e($product['name']) ?></h1>

        <div class="product-price-row">
            <h2>₹<?= number_format((float)$product['price'], 2) ?></h2>
            <span class="product-stock">
                <?= (int)$product['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?>
            </span>
        </div>

        <div class="product-meta">
            <span>Premium quality</span>
            <span>Fresh arrival</span>
            <span><?= (int)$product['stock'] ?> units left</span>
        </div>

        <p class="product-description"><?= e($product['description']) ?></p>

        <?php if ((int)$product['stock'] > 0): ?>
            <form method="POST" class="product-purchase-form">
                <label for="quantity">Quantity</label>
                <div class="quantity-row">
                    <input
                        type="number"
                        id="quantity"
                        name="quantity"
                        value="1"
                        min="1"
                        max="<?= (int)$product['stock'] ?>"
                        required
                    >
                    <button type="submit" class="cart-button">Add to Cart</button>
                </div>
            </form>
        <?php else: ?>
            <button type="button" class="cart-button" disabled>Out of Stock</button>
        <?php endif; ?>
    </div>
</section>

</body>
</html>
