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

$stmt = $conn->prepare("
SELECT
    employees.*,
    departments.department_name,
    positions.position_name,
    positions.basic_salary
FROM employees
LEFT JOIN departments
    ON employees.department_id = departments.department_id
LEFT JOIN positions
    ON employees.position_id = positions.position_id
WHERE employees.employee_id = ?
");

$stmt->bind_param("i", $employee_id);
$stmt->execute();

$result = $stmt->get_result();
$employee = $result->fetch_assoc();

$stmt->close();
?>




<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>My Profile</title>

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

.profile-img{
    width:130px;
    height:130px;
    border-radius:50%;
    object-fit:cover;
    border:4px solid #0291DA;
}

.info-title{
    font-weight:bold;
    color:#1C5FA2;
}

</style>

</head>

<body>




<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-9">

<div class="card shadow">

<div class="card-header">

<h3>

My Profile

</h3>

</div>

<div class="card-body">





<div class="text-center mb-4">

<img

src="../assets/images/default-user.png"

class="profile-img">

<h3 class="mt-3">

<?= htmlspecialchars($employee['first_name']." ".$employee['last_name']); ?>

</h3>

<p class="text-muted">

<?= htmlspecialchars($employee['position_name']); ?>

</p>

</div>





<table class="table table-bordered">

<tr>

<th width="35%">Employee Number</th>

<td><?= htmlspecialchars($employee['employee_number']); ?></td>

</tr>

<tr>

<th>First Name</th>

<td><?= htmlspecialchars($employee['first_name']); ?></td>

</tr>

<tr>

<th>Last Name</th>

<td><?= htmlspecialchars($employee['last_name']); ?></td>

</tr>

<tr>

<th>Email</th>

<td><?= htmlspecialchars($employee['email']); ?></td>

</tr>

<tr>

<th>Phone</th>

<td><?= htmlspecialchars($employee['phone']); ?></td>

</tr>

<tr>

<th>Gender</th>

<td><?= htmlspecialchars($employee['gender']); ?></td>

</tr>

<tr>

<th>Department</th>

<td><?= htmlspecialchars($employee['department_name']); ?></td>

</tr>

<tr>

<th>Position</th>

<td><?= htmlspecialchars($employee['position_name']); ?></td>

</tr>

<tr>

<th>Date Employed</th>

<td><?= htmlspecialchars($employee['employment_date']); ?></td>

</tr>

<tr>

<th>Salary</th>

<td>

₦<?= number_format($employee['basic_salary'],2); ?>

</td>

</tr>

</table>





<div class="mt-4">

<a

href="edit-profile.php"

class="btn text-white"

style="background:#0291DA;">

Edit Profile

</a>

<a

href="change-password.php"

class="btn btn-warning">

Change Password

</a>

<a

href="dashboard.php"

class="btn btn-secondary">

Dashboard

</a>

</div>





</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>