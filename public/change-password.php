<?php

session_start();


if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}


require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/models/User.php';


$userModel = new User($db);


$message = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $currentPassword = $_POST['current_password'] ?? "";
    $newPassword = $_POST['new_password'] ?? "";
    $confirmPassword = $_POST['confirm_password'] ?? "";


    $userId = (int)$_SESSION['user']['id'];


    $user = $userModel->findById($userId);



    if (!$user) {

        $message = "User not found";

    }

    elseif (!password_verify(
        $currentPassword,
        $user['password_hash']
    )) {

        $message = "Current password is incorrect";

    }

    elseif ($newPassword !== $confirmPassword) {

        $message = "Passwords do not match";

    }

    elseif (strlen($newPassword) < 6) {

        $message = "Password must be at least 6 characters";

    }

    else {


        $newHash = password_hash(
            $newPassword,
            PASSWORD_DEFAULT
        );


        $userModel->updatePassword(
            $userId,
            $newHash
        );


        $message = "Password updated successfully";

    }

}

?>


<!DOCTYPE html>
<html>

<head>

<title>
Change Password | AquaShop
</title>

<link rel="stylesheet" href="css/style.css">

</head>


<body>


<h1>
🔐 Change Password
</h1>


<div class="form-container">


<?php if($message): ?>

<p>
<?= htmlspecialchars($message) ?>
</p>

<?php endif; ?>



<form method="POST">


<label>
Current Password
</label>

<br>

<input
type="password"
name="current_password"
required
>


<br><br>


<label>
New Password
</label>

<br>

<input
type="password"
name="new_password"
required
>


<br><br>


<label>
Confirm New Password
</label>

<br>

<input
type="password"
name="confirm_password"
required
>


<br><br>


<button type="submit">
Update Password
</button>


</form>


</div>


</body>

</html>
