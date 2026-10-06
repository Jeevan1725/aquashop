<?php

session_start();

require_once __DIR__ . '/../../app/services/AdminAuth.php';

AdminAuth::check();

require_once __DIR__ . '/../../app/bootstrap.php';
require_once __DIR__ . '/../../app/services/CSRF.php';


$id = (int)($_GET['id'] ?? 0);


if($id <= 0)
{
    die("Invalid user");
}



$stmt = $db->prepare("

SELECT

u.id,
u.name,
u.email,
u.role_id,
u.status

FROM users u

WHERE u.id = ?

");


$stmt->execute([$id]);


$user = $stmt->fetch();



if(!$user)
{
    die("User not found");
}



if($_SERVER['REQUEST_METHOD']=="POST")
{

    if(
        !isset($_POST['csrf_token']) ||
        !CSRF::verify($_POST['csrf_token'])
    ){
        die("Invalid CSRF token");
    }

    $role =
        (int)$_POST['role_id'];


    $status =
        $_POST['status'];



    $allowedRoles = [1,2,3];


    $allowedStatus = [
        'active',
        'blocked'
    ];



    if(
        !in_array($role,$allowedRoles) ||
        !in_array($status,$allowedStatus)
    )
    {
        die("Invalid input");
    }



    $stmt = $db->prepare("

    UPDATE users

    SET
    role_id=?,
    status=?

    WHERE id=?

    ");



    $stmt->execute([

        $role,
        $status,
        $id

    ]);



    header("Location: users.php");

    exit;

}



$roles = $db->query("
SELECT id,name
FROM roles
")->fetchAll();


?>
<!DOCTYPE html>
<html>

<head>

<title>
Edit User | AquaShop Admin
</title>

<link rel="stylesheet" href="../css/style.css">

</head>


<body>


<?php require_once __DIR__ . "/../../app/views/navbar.php"; ?>




<section class="auth-container">


<div class="auth-card">


<h1>
👤 Edit User
</h1>



<div class="order-card">


<p>

<strong>Name:</strong>

<?= htmlspecialchars($user['name']) ?>

</p>



<p>

<strong>Email:</strong>

<?= htmlspecialchars($user['email']) ?>

</p>


</div>




<form method="POST" class="auth-form">



<input
type="hidden"
name="csrf_token"
value="<?= CSRF::generate() ?>"
>




<label>
Role
</label>


<select name="role_id">


<?php foreach($roles as $role): ?>


<option

value="<?= $role['id'] ?>"

<?= $user['role_id']==$role['id']
?'selected'
:'' ?>

>


<?= htmlspecialchars($role['name']) ?>


</option>


<?php endforeach; ?>


</select>




<label>
Status
</label>


<select name="status">


<option
value="active"

<?= $user['status']=="active"
?'selected'
:'' ?>

>

Active

</option>



<option
value="blocked"

<?= $user['status']=="blocked"
?'selected'
:'' ?>

>

Blocked

</option>


</select>



<button type="submit">

Update User

</button>



</form>



<br>


<a class="cart-button"
href="users.php">

← Back Users

</a>



</div>


</section>



</body>

</html>