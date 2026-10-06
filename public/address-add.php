<?php

session_start();

require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/Address.php';


if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}


$userId = (int)$_SESSION['user']['id'];

$addressModel = new Address($db);

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

        $addressModel->create(
            $userId,
            $fullName,
            $phone,
            $address1,
            $address2,
            $city,
            $state,
            $postal
        );

        header("Location: addresses.php");
        exit;
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Add Address | AquaShop</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

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

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        .form-group textarea {
            min-height: 90px;
            resize: vertical;
        }

        .address-submit {
            background: #087e8b;
            color: white;
            border: none;
            padding: 13px 22px;
            border-radius: 7px;
            cursor: pointer;
        }

        .address-submit:hover {
            background: #066875;
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

    <h1>
        📍 Add New Address
    </h1>


    <div class="address-form">


        <?php if ($error !== ''): ?>

            <div class="error-message">

                <?= htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <form method="POST">


            <div class="form-group">

                <label for="full_name">
                    Full Name *
                </label>

                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    maxlength="100"
                    required
                >

            </div>


            <div class="form-group">

                <label for="phone">
                    Phone *
                </label>

                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    maxlength="20"
                    required
                >

            </div>


            <div class="form-group">

                <label for="address_line1">
                    Address Line 1 *
                </label>

                <input
                    type="text"
                    id="address_line1"
                    name="address_line1"
                    maxlength="255"
                    required
                >

            </div>


            <div class="form-group">

                <label for="address_line2">
                    Address Line 2
                </label>

                <input
                    type="text"
                    id="address_line2"
                    name="address_line2"
                    maxlength="255"
                >

            </div>


            <div class="form-group">

                <label for="city">
                    City *
                </label>

                <input
                    type="text"
                    id="city"
                    name="city"
                    maxlength="100"
                    required
                >

            </div>


            <div class="form-group">

                <label for="state">
                    State *
                </label>

                <input
                    type="text"
                    id="state"
                    name="state"
                    maxlength="100"
                    required
                >

            </div>


            <div class="form-group">

                <label for="postal_code">
                    Postal Code *
                </label>

                <input
                    type="text"
                    id="postal_code"
                    name="postal_code"
                    maxlength="20"
                    required
                >

            </div>


            <button
                type="submit"
                class="address-submit"
            >
                Save Address
            </button>


        </form>


        <a
            href="addresses.php"
            class="back-link"
        >
            ← Back to Addresses
        </a>


    </div>


</section>


</body>

</html>
