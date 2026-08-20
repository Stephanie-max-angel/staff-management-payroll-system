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

// Retrieve employee details
$stmt = $conn->prepare("
SELECT *
FROM employees
WHERE employee_id = ?
");

$stmt->bind_param("i", $employee_id);
$stmt->execute();

$result = $stmt->get_result();
$employee = $result->fetch_assoc();
$stmt->close();






if(isset($_POST['update'])){

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if(empty($first_name) || empty($last_name) || empty($email)){

        $error = "Please complete all required fields.";

    }else{

        // Check if another employee already uses this email
        $check = $conn->prepare("
        SELECT employee_id
        FROM employees
        WHERE email = ?
        AND employee_id != ?
        ");

        $check->bind_param("si",$email,$employee_id);
        $check->execute();
        $check->store_result();

        if($check->num_rows > 0){

            $error = "Email address already exists.";

        }else{

            $stmt = $conn->prepare("
            UPDATE employees

            SET

            first_name=?,
            last_name=?,
            email=?,
            phone=?,
            address=?

            WHERE employee_id=?
            ");

            $stmt->bind_param(
                "sssssi",
                $first_name,
                $last_name,
                $email,
                $phone,
                $address,
                $employee_id
            );

            if($stmt->execute()){

                $_SESSION['employee_name'] = $first_name;

                $success = "Profile updated successfully.";

                // Reload updated information
                $refresh = $conn->prepare("
                SELECT *
                FROM employees
                WHERE employee_id=?
                ");

                $refresh->bind_param("i",$employee_id);
                $refresh->execute();

                $employee = $refresh->get_result()->fetch_assoc();

                $refresh->close();

            }else{

                $error = "Unable to update profile.";

            }

            $stmt->close();

        }

        $check->close();

    }

}
?>






<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Edit Profile</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
    background:#F2F4F5;
}

.card-header{
    background:#1C5FA2;
    color:white;
}

</style>

</head>

<body>





<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card shadow">

<div class="card-header">

<h3>Edit Profile</h3>

</div>

<div class="card-body">





<?php if($error!=""){ ?>

<div class="alert alert-danger">

<?= $error; ?>

</div>

<?php } ?>

<?php if($success!=""){ ?>

<div class="alert alert-success">

<?= $success; ?>

</div>

<?php } ?>






<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label>First Name</label>

<input
type="text"
name="first_name"
class="form-control"
value="<?= htmlspecialchars($employee['first_name']); ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label>Last Name</label>

<input
type="text"
name="last_name"
class="form-control"
value="<?= htmlspecialchars($employee['last_name']); ?>"
required>

</div>

</div>

<div class="mb-3">

<label>Email</label>

<input
type="email"
name="email"
class="form-control"
value="<?= htmlspecialchars($employee['email']); ?>"
required>

</div>

<div class="mb-3">

<label>Phone Number</label>

<input
type="text"
name="phone"
class="form-control"
value="<?= htmlspecialchars($employee['phone']); ?>">

</div>

<div class="mb-3">

<label>Residential Address</label>

<textarea
name="address"
rows="3"
class="form-control"><?= htmlspecialchars($employee['address']); ?></textarea>

</div>



<hr>

<h5 class="mb-3">Employment Information</h5>

<div class="row">

<div class="col-md-6 mb-3">

<label>Employee Number</label>

<input
class="form-control"
value="<?= htmlspecialchars($employee['employee_number']); ?>"
readonly>

</div>

<div class="col-md-6 mb-3">

<label>Department</label>

<input
class="form-control"
value="<?= htmlspecialchars($employee['department_id']); ?>"
readonly>

</div>

<div class="col-md-6 mb-3">

<label>Position</label>

<input
class="form-control"
value="<?= htmlspecialchars($employee['position_id']); ?>"
readonly>

</div>



</div>


<button
type="submit"
name="update"
class="btn text-white"
style="background:#0291DA;">

Save Changes

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