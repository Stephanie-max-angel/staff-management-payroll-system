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
    header("Location: payroll.php");
    exit();
}

$payroll_id = intval($_GET['id']);





$stmt = $conn->prepare("
SELECT

payroll.*,

employees.employee_number,

employees.first_name,

employees.last_name,

employees.email,

employees.phone,

departments.department_name,

positions.position_name

FROM payroll

INNER JOIN employees
ON payroll.employee_id = employees.employee_id

INNER JOIN departments
ON employees.department_id = departments.department_id

INNER JOIN positions
ON employees.position_id = positions.position_id

WHERE payroll.payroll_id = ?

");

$stmt->bind_param("i",$payroll_id);

$stmt->execute();

$result = $stmt->get_result();

$payroll = $result->fetch_assoc();

$stmt->close();

if(!$payroll){

    header("Location: payroll.php");

    exit();

}
?>



<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Payslip</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{

background:#f5f5f5;

}

.payslip{

background:white;

padding:40px;

box-shadow:0 0 15px rgba(0,0,0,.15);

}

.company{

color:#1C5FA2;

font-weight:bold;

font-size:30px;

}
.btn{
  margin-top: 40px;
}

@media print{

.no-print{

display:none;

}

}

</style>

</head>

<body>





<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-lg-10">

<div class="payslip">





<div class="text-center mb-4">

<div class="company">

MUDIAME UNIVERSITY IRRUA

</div>

<h5>

Employee Payroll Payslip

</h5>

<hr>

</div>





<div class="row">

<div class="col-md-6">

<strong>Employee No:</strong>

<?= htmlspecialchars($payroll['employee_number']); ?>

<br>

<strong>Name:</strong>

<?= htmlspecialchars($payroll['first_name']." ".$payroll['last_name']); ?>

<br>

<strong>Department:</strong>

<?= htmlspecialchars($payroll['department_name']); ?>

<br>

<strong>Position:</strong>

<?= htmlspecialchars($payroll['position_name']); ?>

</div>

<div class="col-md-6">

<strong>Email:</strong>

<?= htmlspecialchars($payroll['email']); ?>

<br>

<strong>Phone:</strong>

<?= htmlspecialchars($payroll['phone']); ?>

<br>

<strong>Month:</strong>

<?= htmlspecialchars($payroll['payroll_month']); ?>

<br>

<strong>Year:</strong>

<?= htmlspecialchars($payroll['payroll_year']); ?>

</div>

</div>




<table class="table table-bordered mt-4">

<thead class="table-primary">

<tr>

<th>Description</th>

<th class="text-end">Amount (₦)</th>

</tr>

</thead>

<tbody>

<tr>

<td>Basic Salary</td>

<td class="text-end">

<?= number_format($payroll['basic_salary'],2); ?>

</td>

</tr>

<tr>

<td>Total Allowances</td>

<td class="text-end">

<?= number_format($payroll['allowances'],2); ?>

</td>

</tr>

<tr>

<td>Total Deductions</td>

<td class="text-end">

<?= number_format($payroll['deductions'],2); ?>

</td>

</tr>

<tr>

<td>Tax</td>

<td class="text-end">

<?= number_format($payroll['tax'],2); ?>

</td>

</tr>



<tr>

<td>Pension</td>

<td class="text-end">

<?= number_format($payroll['pension'],2); ?>

</td>

</tr>



<tr class="table-success">

<th>Net Salary</th>

<th class="text-end">

₦<?= number_format($payroll['net_salary'],2); ?>

</th>

</tr>

</tbody>

</table>






<div class="mt-3">

<strong>Status:</strong>

<?php

if($payroll['payment_status']=="Paid"){

?>

<span class="badge bg-success">

Paid

</span>

<?php }else{ ?>

<span class="badge bg-warning">

Pending

</span>

<?php } ?>

</div>





<div class="row mt-5">

<div class="col-md-6 text-center">

________________________

<br>

Payroll Officer

</div>

<div class="col-md-6 text-center">

________________________

<br>

Employee Signature

</div>

</div>




<!-- <div class="mt-5">

<button

onclick="window.print();"

class="btn btn-success">

Print Payslip

</button>

<a

href="payroll.php"

class="btn btn-secondary">

Back

</a>
</div> -->



<div class="no-print">

    
    <a
        href="print-payslip.php?id=<?= $payroll['payroll_id']; ?>"
        class="btn btn-success">

        Download

    </a>

    <a
        href="payroll.php"
        class="btn btn-secondary">

        Back

    </a>

</div>







</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>