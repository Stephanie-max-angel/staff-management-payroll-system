<?php
session_start();
require_once("../admin/config/database.php");
require("includes/auth.php");
require_once("includes/header.php");
require_once("includes/sidebar.php");
require_once("includes/navbar.php");

if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

$employee_id = $_SESSION['employee_id'];
$error = "";
$success = "";

// Retrieve employee password
$stmt = $conn->prepare("
SELECT password
FROM employees
WHERE employee_id = ?
");

$stmt->bind_param("i", $employee_id);
$stmt->execute();

$result = $stmt->get_result();
$employee = $result->fetch_assoc();

$stmt->close();







if(isset($_POST['change_password'])){

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if(!password_verify($current_password, $employee['password'])){

        $error = "Current password is incorrect.";

    }elseif(strlen($new_password) < 8){

        $error = "New password must be at least 8 characters.";

    }elseif($new_password != $confirm_password){

        $error = "Passwords do not match.";

    }else{

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
        UPDATE employees
        SET password=?
        WHERE employee_id=?
        ");

        $stmt->bind_param(
            "si",
            $hashed_password,
            $employee_id
        );

        if($stmt->execute()){

            $success = "Password changed successfully.";

        }else{

            $error = "Unable to change password.";

        }

        $stmt->close();

    }

}
?>






<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Change Password</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
    background:#F2F4F5;
}

.card-header{
    background:#27348E;
    color:white;
}

.btn-save{
    background:#0291DA;
    color:white;
}

.btn-save:hover{
    background:#1C5FA2;
    color:white;
}

</style>

</head>

<body>




<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-6">

<div class="card shadow">

<div class="card-header">

<h3>

Change Password

</h3>

</div>

<div class="card-body">






<?php if($error!=""){ ?>

<div class="alert alert-danger">

<?= htmlspecialchars($error); ?>

</div>

<?php } ?>

<?php if($success!=""){ ?>

<div class="alert alert-success">

<?= htmlspecialchars($success); ?>

</div>

<?php } ?>






<form method="POST">

<div class="mb-3">

<label>

Current Password

</label>

<input
type="password"
name="current_password"
class="form-control"
required>

</div>

<div class="mb-3">

<label>

New Password

</label>

<input
type="password"
name="new_password"
class="form-control"
required>

</div>

<div class="mb-3">

<label>

Confirm New Password

</label>

<input
type="password"
name="confirm_password"
class="form-control"
required>

</div>

<button

type="submit"

name="change_password"

class="btn btn-save">

Change Password

</button>

<a

href="profile.php"

class="btn btn-secondary">

Cancel

</a>

</form>




</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>