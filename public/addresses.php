<?php

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/Address.php';
require_once __DIR__ . '/../app/services/CSRF.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['user']['id'];
$addressModel = new Address($db);
$addresses = $addressModel->getByUser($userId);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Addresses | AquaShop</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php require_once __DIR__ . "/../app/views/navbar.php"; ?>

<section style="padding:60px 7%;">
    <h1 style="text-align:center;">📍 My Addresses</h1>

    <div style="text-align:center;margin:35px 0;">
        <a href="address-add.php" style="background:#087e8b;color:white;padding:12px 20px;border-radius:7px;text-decoration:none;">
            + Add New Address
        </a>
    </div>

    <?php if (empty($addresses)): ?>
        <div style="text-align:center;background:white;padding:50px;border-radius:12px;">
            <h2>No addresses found</h2>
            <p>Add an address to make checkout faster.</p>
        </div>
    <?php else: ?>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:25px;">
            <?php foreach ($addresses as $address): ?>
                <div style="background:white;padding:25px;border-radius:12px;box-shadow:0 3px 15px rgba(0,0,0,0.08);">
                    <h3><?= e($address['full_name']) ?></h3>
                    <p><?= e($address['address_line1']) ?><br>
                       <?= e($address['city']) ?>, <?= e($address['state']) ?></p>

                    <div style="display:flex;gap:10px;margin-top:20px;">
                        <a href="address-edit.php?id=<?= (int)$address['id'] ?>"
                           style="background:#087e8b;color:white;padding:9px 14px;border-radius:6px;text-decoration:none;">
                            Edit
                        </a>

                        <form method="POST" action="address-delete.php">
                            <input type="hidden" name="address_id" value="<?= (int)$address['id'] ?>">
                            <?php if (is_secure()): ?>
                                <input type="hidden" name="csrf_token" value="<?= CSRF::generate() ?>">
                            <?php endif; ?>
                            <button type="submit"
                                style="background:#dc3545;color:white;padding:9px 14px;border:none;border-radius:6px;cursor:pointer;">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

</body>
</html>
