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

if (!isset($_GET['id'])) {
    header("Location: payslips.php");
    exit();
}

$employee_id = $_SESSION['employee_id'];
$payroll_id = intval($_GET['id']);

$stmt = $conn->prepare("
SELECT
    payroll.*,
    employees.employee_number,
    employees.first_name,
    employees.last_name,
    departments.department_name,
    positions.position_name

FROM payroll

INNER JOIN employees
    ON payroll.employee_id = employees.employee_id

LEFT JOIN departments
    ON employees.department_id = departments.department_id

LEFT JOIN positions
    ON employees.position_id = positions.position_id

WHERE payroll.payroll_id = ?
AND payroll.employee_id = ?
");

$stmt->bind_param("ii", $payroll_id, $employee_id);
$stmt->execute();

$result = $stmt->get_result();
$payslip = $result->fetch_assoc();

$stmt->close();

if(!$payslip){
    header("Location: payslips.php");
    exit();
}
?>





<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Payslip</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
    background:#F2F4F5;
}

.card-header{
    background:#27348E;
    color:white;
}

.company-name{
    color:#1C5FA2;
    font-weight:bold;
}

.salary-table th{
    width:45%;
}

</style>

</head>

<body>





<div class="container mt-5">

<div class="card shadow">

<div class="card-header">

<h3>

Employee Payslip

</h3>

</div>

<div class="card-body">




<div class="text-center mb-4">

<img

src="../assets/images/logo.png"

width="90">

<h2 class="company-name">

MUDIAME UNIVERSITY IRRUA

</h2>

<p>

Employee Management System

</p>

<hr>

</div>





<table class="table table-bordered">

<tr>

<th>Employee Number</th>

<td><?= htmlspecialchars($payslip['employee_number']); ?></td>

</tr>

<tr>

<th>Employee Name</th>

<td>

<?= htmlspecialchars($payslip['first_name']." ".$payslip['last_name']); ?>

</td>

</tr>

<tr>

<th>Department</th>

<td><?= htmlspecialchars($payslip['department_name']); ?></td>

</tr>

<tr>

<th>Position</th>

<td><?= htmlspecialchars($payslip['position_name']); ?></td>

</tr>

<tr>

<th>Payroll Month</th>

<td><?= htmlspecialchars($payslip['payroll_month']); ?></td>

</tr>

</table>









<table class="table table-bordered salary-table">

<tr>

<th>Basic Salary</th>

<td>₦<?= number_format($payslip['basic_salary'],2); ?></td>

</tr>

<tr>

<th>Allowance</th>

<td>₦<?= number_format($payslip['allowances'],2); ?></td>

</tr>




<tr>

<th>Tax</th>

<td>₦<?= number_format($payslip['tax'],2); ?></td>

</tr>

<tr>

<th>Pension</th>

<td>₦<?= number_format($payslip['pension'],2); ?></td>

</tr>

<tr>

<th>Other Deductions</th>

<td>₦<?= number_format($payslip['deductions'],2); ?></td>

</tr>

<tr class="table-info">

<th>Gross Salary</th>

<td>

<strong>

₦<?= number_format($payslip['net_salary'],2); ?>

</strong>

</td>

</tr>

<tr class="table-success">

<th>Net Salary</th>

<td>

<strong>

₦<?= number_format($payslip['net_salary'],2); ?>

</strong>

</td>

</tr>

</table>





<h5>

Payment Status:

<?php

if($payslip['payment_status']=="Paid"){

?>

<span class="badge bg-success">

Paid

</span>

<?php }else{ ?>

<span class="badge bg-warning">

Pending

</span>

<?php } ?>

</h5>





<div class="mt-4">

<a

href="print-payslips.php?id=<?= $payslip['payroll_id']; ?>"

class="btn btn-success">

Print Payslip

</a>

<a

href="payslips.php"

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