<?php

session_start();

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/Wishlist.php';


// User must be logged in

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}


// Only POST requests

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: products.php");
    exit;
}


$productId = (int)($_POST['product_id'] ?? 0);

if ($productId <= 0) {
    die("Invalid product");
}


$userId = (int)$_SESSION['user']['id'];


$wishlist = new Wishlist($db);

$wishlist->add(
    $userId,
    $productId
);


// Return to the page that submitted the request

$redirect = $_POST['redirect'] ?? 'products.php';


// Prevent external redirects

if (
    !is_string($redirect) ||
    !str_starts_with($redirect, '/')
) {
    $redirect = 'products.php';
}

header("Location: " . $redirect);
exit;
