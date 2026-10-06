<?php

require_once __DIR__ . '/../../app/services/AdminAuth.php';

AdminAuth::check();

require_once __DIR__ . '/../../app/bootstrap.php';
require_once __DIR__ . '/../../app/services/CSRF.php';

$id = (int) ($_GET['id'] ?? 0);


if ($id <= 0) {
    die("Invalid product.");
}


/*
 * Fetch product
 */
$stmt = $db->prepare("
    SELECT *
    FROM products
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

$product = $stmt->fetch();


if (!$product) {
    die("Product not found.");
}



/*
 * Update product
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    if(
        !isset($_POST['csrf_token']) ||
        !CSRF::verify($_POST['csrf_token'])
    ){
        die("Invalid CSRF token");
    }

    $name =
        trim($_POST['name']);


    $category =
        (int) $_POST['category_id'];


    $description =
        trim($_POST['description']);


    $price =
        (float) $_POST['price'];


    $stock =
        (int) $_POST['stock'];


    $status =
        $_POST['status'];

    
    $image = $product['image'];


if(
    isset($_FILES['image']) &&
    $_FILES['image']['error'] === 0
){

    $fileName =
        time().'_'.basename($_FILES['image']['name']);


    $uploadDirectory =
        __DIR__.'/../uploads/products/';


    if(!is_dir($uploadDirectory)){
        mkdir($uploadDirectory,0755,true);
    }


    move_uploaded_file(
        $_FILES['image']['tmp_name'],
        $uploadDirectory.$fileName
    );


    $image = $fileName;

}



    $slug =
        strtolower(
            preg_replace(
                '/[^a-zA-Z0-9]+/',
                '-',
                $name
            )
        );



    $stmt = $db->prepare("
        UPDATE products

        SET
        category_id = ?,
        name = ?,
        slug = ?,
        description = ?,
        price = ?,
        stock = ?,
        status = ?,
        image = ?

        WHERE id = ?
    ");



    $stmt->execute([

        $category,
        $name,
        $slug,
        $description,
        $price,
        $stock,
        $status,
        $image,
        $id

    ]);



    header("Location: products.php");

    exit;

}



/*
 * Get categories
 */

$categories = $db->query("
    SELECT id,name
    FROM categories
    ORDER BY name
")->fetchAll();


?>


<!DOCTYPE html>

<html>

<head>

<title>
Edit Product
</title>

</head>


<body>


<h1>
Edit Product 🐟
</h1>


<form method="POST" enctype="multipart/form-data">

<input 
type="hidden"
name="csrf_token"
value="<?= CSRF::generate() ?>"
>


<label>
Product Name
</label>

<br>

<input
name="name"
value="<?= htmlspecialchars($product['name']) ?>"
required
>


<br><br>



<label>
Category
</label>

<br>


<select name="category_id">


<?php foreach($categories as $category): ?>


<option

value="<?= $category['id'] ?>"

<?= 
$product['category_id'] == $category['id']
? 'selected'
: ''
?>

>

<?= htmlspecialchars($category['name']) ?>

</option>


<?php endforeach; ?>


</select>


<br><br>



<label>
Description
</label>

<br>


<textarea name="description">

<?= htmlspecialchars($product['description']) ?>

</textarea>


<br><br>



<label>
Price
</label>

<br>


<input

type="number"

step="0.01"

name="price"

value="<?= $product['price'] ?>"

required

>


<br><br>



<label>
Stock
</label>

<br>


<input

type="number"

name="stock"

value="<?= $product['stock'] ?>"

required

>


<br><br>



<label>
Status
</label>

<br>


<select name="status">


<option

value="active"

<?= $product['status']=="active"
? "selected"
: ""
?>

>

Active

</option>



<option

value="inactive"

<?= $product['status']=="inactive"
? "selected"
: ""
?>

>

Inactive

</option>


</select>


<br><br>

<label>
Product Image
</label>

<br>

<input
type="file"
name="image"
accept="image/*"
>

<br><br>


<?php if(!empty($product['image'])): ?>

<img 
src="../uploads/products/<?= htmlspecialchars($product['image']) ?>"
width="120"
>

<?php endif; ?>

<br><br>



<button>

Update Product

</button>


</form>


<br>


<a href="products.php">
Back
</a>


</body>

</html>
