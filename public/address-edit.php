<?php

session_start();

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/Address.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['user']['id'];

$addressId = isset($_GET['id'])
    ? (int)$_GET['id']
    : 0;

if ($addressId <= 0) {
    die("Invalid address");
}

$addressModel = new Address($db);

/*
|--------------------------------------------------------------------------
| VULNERABLE: no ownership check — IDOR possible
| SECURE:     ownership enforced by getById()
|--------------------------------------------------------------------------
*/
if (is_vulnerable()) {

    // VULN: fetch by ID only — anyone can load anyone's address
    $stmt = $db->prepare("SELECT * FROM addresses WHERE id = ? LIMIT 1");
    $stmt->execute([$addressId]);
    $address = $stmt->fetch() ?: null;

} else {

    // SECURE: ownership enforced in SQL
    $address = $addressModel->getById($addressId, $userId);
}

if (!$address) {
    die("Address not found");
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address1 = trim($_POST['address_line1'] ?? '');
    $address2 = trim($_POST['address_line2'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $postal = trim($_POST['postal_code'] ?? '');

    if (
        $fullName === '' ||
        $phone === '' ||
        $address1 === '' ||
        $city === '' ||
        $state === '' ||
        $postal === ''
    ) {

        $error = "Please fill in all required fields.";

    } else {

        if (is_vulnerable()) {

            // VULN: update by ID only — no user_id constraint
            $stmt = $db->prepare("
                UPDATE addresses
                SET
                    full_name = ?,
                    phone = ?,
                    address_line1 = ?,
                    address_line2 = ?,
                    city = ?,
                    state = ?,
                    postal_code = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $fullName,
                $phone,
                $address1,
                $address2,
                $city,
                $state,
                $postal,
                $addressId
            ]);

            header("Location: addresses.php");
            exit;

        } else {

            // SECURE: ownership enforced in SQL
            $updated = $addressModel->update(
                $addressId,
                $userId,
                $fullName,
                $phone,
                $address1,
                $address2,
                $city,
                $state,
                $postal
            );

            if ($updated) {
                header("Location: addresses.php");
                exit;
            } else {
                $error = "Unable to update address.";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Address | AquaShop</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .address-form-page {
            padding: 60px 7%;
            max-width: 800px;
            margin: auto;
        }
        .address-form-page h1 {
            text-align: center;
            margin-bottom: 35px;
        }
        .address-form {
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }
        .address-submit {
            background: #087e8b;
            color: white;
            border: none;
            padding: 13px 22px;
            border-radius: 7px;
            cursor: pointer;
        }
        .error-message {
            background: #f8d7da;
            color: #842029;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #087e8b;
        }
    </style>
</head>
<body>

<?php require_once __DIR__ . "/../app/views/navbar.php"; ?>

<section class="address-form-page">
    <h1>✏️ Edit Address</h1>

    <div class="address-form">
        <?php if ($error !== ''): ?>
            <div class="error-message">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <?php if (is_vulnerable()): ?>
            <div class="error-message" style="background:#fff3cd;color:#856404;">
                ⚠️ <strong>VULNERABLE MODE</strong> — no ownership check on this address ID.
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" id="full_name" name="full_name" maxlength="100"
                    value="<?= e($address['full_name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone *</label>
                <input type="tel" id="phone" name="phone" maxlength="20"
                    value="<?= e($address['phone']) ?>" required>
            </div>
            <div class="form-group">
                <label for="address_line1">Address Line 1 *</label>
                <input type="text" id="address_line1" name="address_line1" maxlength="255"
                    value="<?= e($address['address_line1']) ?>" required>
            </div>
            <div class="form-group">
                <label for="address_line2">Address Line 2</label>
                <input type="text" id="address_line2" name="address_line2" maxlength="255"
                    value="<?= e($address['address_line2'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="city">City *</label>
                <input type="text" id="city" name="city" maxlength="100"
                    value="<?= e($address['city']) ?>" required>
            </div>
            <div class="form-group">
                <label for="state">State *</label>
                <input type="text" id="state" name="state" maxlength="100"
                    value="<?= e($address['state']) ?>" required>
            </div>
            <div class="form-group">
                <label for="postal_code">Postal Code *</label>
                <input type="text" id="postal_code" name="postal_code" maxlength="20"
                    value="<?= e($address['postal_code']) ?>" required>
            </div>
            <button type="submit" class="address-submit">Update Address</button>
        </form>

        <a href="addresses.php" class="back-link">← Back to Addresses</a>
    </div>
</section>

</body>
</html>
