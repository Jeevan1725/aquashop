<?php

session_start();

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/Wishlist.php';


if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: wishlist.php");
    exit;
}


$productId = (int)($_POST['product_id'] ?? 0);

if ($productId <= 0) {
    die("Invalid product");
}


$userId = (int)$_SESSION['user']['id'];


$wishlist = new Wishlist($db);

$wishlist->remove(
    $userId,
    $productId
);


header("Location: wishlist.php");
exit;
