<?php

session_start();


require_once __DIR__ . '/../app/bootstrap.php';

require_once __DIR__ . '/../app/models/Cart.php';



if(!isset($_SESSION['user']))
{
    header("Location: login.php");
    exit;
}



$productId = (int)($_POST['product_id'] ?? 0);



if($productId <= 0)
{
    die("Invalid product");
}



$cartModel = new Cart($db);



$cartId = $cartModel->getOrCreateCart(

    $_SESSION['user']['id']

);



$cartModel->addItem(

    $cartId,

    $productId,

    1

);



header("Location: cart.php");

exit;
