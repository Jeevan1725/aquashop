<?php

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/Address.php';
require_once __DIR__ . '/../app/services/CSRF.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['user']['id'];

if (is_secure()) {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: addresses.php");
        exit;
    }

    CSRF::verify($_POST['csrf_token'] ?? '');
    $addressId = (int)($_POST['address_id'] ?? 0);

} else {
    // VULN: any method, no token
    $addressId = (int)($_REQUEST['address_id'] ?? 0);
}

if ($addressId <= 0) {
    die("Invalid address");
}

$addressModel = new Address($db);

if (is_secure()) {
    $deleted = $addressModel->delete($addressId, $userId);
} else {
    $stmt = $db->prepare("DELETE FROM addresses WHERE id = ?");
    $deleted = $stmt->execute([$addressId]);
}

if (!$deleted) {
    die("Unable to delete address");
}

header("Location: addresses.php");
exit;
