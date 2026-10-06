<?php

session_start();

require_once __DIR__ . '/../../app/services/AdminAuth.php';

AdminAuth::check();

require_once __DIR__ . '/../../app/bootstrap.php';

require_once __DIR__ . '/../../app/services/CSRF.php';


if($_SERVER['REQUEST_METHOD'] !== 'POST')
{
    die("METHOD = " . $_SERVER['REQUEST_METHOD']);
}


CSRF::verify(
    $_POST['csrf_token'] ?? ''
);


$productId = (int)($_POST['product_id'] ?? 0);


if($productId <= 0)
{
    die("Invalid product");
}


$stmt = $db->prepare("
    UPDATE products
    SET status='inactive'
    WHERE id = ?
");


$stmt->execute([
    $productId
]);


header("Location: products.php");

exit;