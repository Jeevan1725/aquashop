<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}


require_once __DIR__ . '/../app/bootstrap.php';


$userId = (int) $_SESSION['user']['id'];



$stmt = $db->prepare("
    SELECT
        name,
        email,
        phone
    FROM users
    WHERE id = ?
    LIMIT 1
");


$stmt->execute([$userId]);


$user = $stmt->fetch();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $name = trim($_POST['name']);

    $phone = trim($_POST['phone']);



    $update = $db->prepare("
        UPDATE users
        SET name = ?,
            phone = ?
        WHERE id = ?
    ");


    $update->execute([
        $name,
        $phone,
        $userId
    ]);



    $_SESSION['user']['name'] = $name;


    header("Location: profile.php");

    exit;

}


?>


<!DOCTYPE html>

<html>

<head>

<title>
Edit Profile | AquaShop
</title>


<link rel="stylesheet" href="css/style.css">


</head>


<body>


<header class="navbar">

<div class="logo">
🐠 AquaShop
</div>


<nav>

<a href="index.php">
Home
</a>

<a href="products.php">
Shop
</a>

<a href="cart.php">
Cart 🛒
</a>

<a href="wishlist.php">
❤️ Wishlist
</a>

<a href="profile.php">
Account 👤
</a>

</nav>

</header>



<section class="products-page">


<h1>
Edit Profile ✏️
</h1>





<div class="product-card">


<div class="product-info">



<form method="POST">


<label>
Name
</label>

<br>


<input
type="text"
name="name"
value="<?= htmlspecialchars($user['name']) ?>"
required
>


<br><br>



<label>
Email
</label>

<br>


<input
type="email"
value="<?= htmlspecialchars($user['email']) ?>"
disabled
>


<br><br>



<label>
Phone
</label>

<br>


<input
type="text"
name="phone"
value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
>


<br><br>



<button class="cart-button">
Update Profile
</button>


</form>


</div>


</div>


</section>


</body>

</html>
