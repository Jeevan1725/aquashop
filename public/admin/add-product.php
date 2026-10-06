<?php

session_start();

require_once __DIR__ . '/../../app/services/AdminAuth.php';
AdminAuth::check();

require_once __DIR__ . '/../../app/bootstrap.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $category = (int) $_POST['category_id'];
    $description = trim($_POST['description']);
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];
    $status = $_POST['status'];

    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));

    $image = null;

    if (
        isset($_FILES['image']) &&
        $_FILES['image']['error'] === 0
    ) {

        $uploadDirectory = __DIR__ . '/../uploads/products/';

        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }

        if (is_secure()) {

            /*
            |--------------------------------------------------------------------------
            | SECURE upload
            |--------------------------------------------------------------------------
            | - whitelist extension
            | - whitelist MIME type
            | - randomize filename
            */
            $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            $originalName = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowedExt, true)) {
                die("Invalid file type. Only images allowed.");
            }

            $mime = mime_content_type($_FILES['image']['tmp_name']);
            $allowedMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            if (!in_array($mime, $allowedMime, true)) {
                die("Invalid MIME type.");
            }

            $fileName = bin2hex(random_bytes(16)) . '.' . $ext;
            $uploadPath = $uploadDirectory . $fileName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                die("Upload failed.");
            }

            $image = $fileName;

        } else {

            /*
            |--------------------------------------------------------------------------
            | VULNERABLE upload
            |--------------------------------------------------------------------------
            | - no extension check
            | - no MIME check
            | - preserves original filename
            | - .php files uploaded here execute when requested
            */
            $fileName = time() . "_" . basename($_FILES['image']['name']);
            $uploadPath = $uploadDirectory . $fileName;

            move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);

            $image = $fileName;
        }
    }


    $stmt = $db->prepare("
        INSERT INTO products
        (category_id, name, slug, description, price, stock, status, image)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $category,
        $name,
        $slug,
        $description,
        $price,
        $stock,
        $status,
        $image
    ]);

    header("Location: products.php");
    exit;
}


$categories = $db->query("
    SELECT id, name FROM categories ORDER BY name
")->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
<title>Add Product | AquaShop Admin</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php require_once __DIR__ . "/../../app/views/navbar.php"; ?>

<section class="auth-container">
<div class="auth-card">

<h1>🐟 Add New Product</h1>

<?php if (is_vulnerable()): ?>
    <div style="background:#fff3cd;color:#856404;padding:12px;border-radius:6px;margin-bottom:15px;">
        ⚠️ <strong>VULNERABLE MODE</strong> — no file type validation. Try uploading a .php file.
    </div>
<?php else: ?>
    <div style="background:#d4edda;color:#155724;padding:12px;border-radius:6px;margin-bottom:15px;">
        🛡️ <strong>SECURE MODE</strong> — only image files allowed.
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="auth-form">

    <label>Product Name</label>
    <input type="text" name="name" placeholder="Product name" required>

    <label>Category</label>
    <select name="category_id" required>
        <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>">
                <?= e($category['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Description</label>
    <textarea name="description" placeholder="Product description"></textarea>

    <label>Price</label>
    <input type="number" name="price" step="0.01" placeholder="Price" required>

    <label>Stock</label>
    <input type="number" name="stock" placeholder="Available stock" required>

    <label>Status</label>
    <select name="status">
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select>

    <label>Product Image</label>
    <input type="file" name="image">

    <button type="submit">➕ Add Product</button>

</form>

<br>
<a class="cart-button" href="products.php">← Back Products</a>

</div>
</section>

</body>
</html>
