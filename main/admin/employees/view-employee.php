<?php
session_start();
require_once("../config/database.php");
require_once("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/sidebar.php");
require_once("../includes/navbar.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: employees.php");
    exit();
}

$employee_id = intval($_GET['id']);


$stmt = $conn->prepare("
SELECT
    employees.*,
    departments.department_name,
    positions.position_name,
    positions.basic_salary
FROM employees
INNER JOIN departments
ON employees.department_id = departments.department_id
INNER JOIN positions
ON employees.position_id = positions.position_id
WHERE employees.employee_id = ?
");

$stmt->bind_param("i", $employee_id);

$stmt->execute();

$result = $stmt->get_result();

$employee = $result->fetch_assoc();

$stmt->close();

if(!$employee){
    header("Location: employees.php");
    exit();
}
?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Employee Profile</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">



<div class="container mt-5">

<div class="card shadow">

<div
class="card-header text-white"
style="background:#1C5FA2;">

<h3>Employee Profile</h3>

</div>

<div class="card-body">


<div class="text-center mb-4">

<?php if(!empty($employee['photo'])){ ?>

<img

src="../uploads/<?= htmlspecialchars($employee['photo']); ?>"

class="rounded-circle border"

width="180"

height="180">

<?php } else { ?>

<img

src="../assets/images/default-user.png"

class="rounded-circle border"

width="180"

height="180">

<?php } ?>

</div>



<table class="table table-bordered">

<tr>

<th width="30%">Employee Number</th>

<td><?= htmlspecialchars($employee['employee_number']); ?></td>

</tr>

<tr>

<th>Full Name</th>

<td>

<?= htmlspecialchars($employee['first_name']." ".$employee['last_name']); ?>

</td>

</tr>

<tr>

<th>Gender</th>

<td><?= htmlspecialchars($employee['gender']); ?></td>

</tr>

<tr>

<th>Date of Birth</th>

<td><?= htmlspecialchars($employee['date_of_birth']); ?></td>

</tr>

<tr>

<th>Email</th>

<td><?= htmlspecialchars($employee['email']); ?></td>

</tr>

<tr>

<th>Phone Number</th>

<td><?= htmlspecialchars($employee['phone']); ?></td>

</tr>

<tr>

<th>Address</th>

<td><?= htmlspecialchars($employee['address']); ?></td>

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

<th>Basic Salary</th>

<td>

₦<?= number_format($employee['basic_salary'],2); ?>

</td>

</tr>

<tr>

<th>Employment Type</th>

<td><?= htmlspecialchars($employee['employment_type']); ?></td>

</tr>

<tr>

<th>Employment Date</th>

<td><?= htmlspecialchars($employee['employment_date']); ?></td>

</tr>

<tr>

<th>Status</th>

<td>

<?php

if($employee['status']=="Active"){

?>

<span class="badge bg-success">

Active

</span>

<?php }else{ ?>

<span class="badge bg-danger">

Inactive

</span>

<?php } ?>

</td>

</tr>

</table>



<div class="mt-4">

<a

href="edit-employee.php?id=<?= $employee['employee_id']; ?>"

class="btn btn-warning">

Edit Employee

</a>

<a

href="employees.php"

class="btn btn-secondary">

Back

</a>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

