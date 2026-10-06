<?php

require_once __DIR__ . '/../app/services/Auth.php';

if(!Auth::check())
{
    header("Location: login.php");
    exit;
}


require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/Product.php';
require_once __DIR__.'/../app/views/navbar.php';

$productModel = new Product($db);


$search = $_GET['search'] ?? null;


$products = $productModel->getAll($search);


$user = Auth::user();

?>


<!DOCTYPE html>
<html>

<head>

<title>
Shop | AquaShop
</title>


<link rel="stylesheet" href="css/style.css">


</head>


<body>





<section class="products-page">


<h1>
🐟 Browse Products
</h1>



<form method="GET">

<input
type="text"
name="search"
placeholder="Search fish..."
value="<?= htmlspecialchars($search ?? '') ?>"
>


<button>
Search
</button>


</form>



<br>



<div class="category-grid">


<?php foreach($products as $product): ?>


<div class="category-card">


<?php if(!empty($product['image'])): ?>

<img
src="uploads/products/<?= htmlspecialchars($product['image']) ?>"
width="150"
>

<?php else: ?>

🐟

<?php endif; ?>



<h3>
<?= htmlspecialchars($product['name']) ?>
</h3>



<p>
Category:
<?= htmlspecialchars($product['category']) ?>
</p>



<p>
₹<?= number_format($product['price'],2) ?>
</p>



<p>
Stock:
<?= $product['stock'] ?>
</p>



<a href="product.php?slug=<?= urlencode($product['slug']) ?>">
View Product
</a>



</div>


<?php endforeach; ?>


</div>


</section>


</body>

</html>
