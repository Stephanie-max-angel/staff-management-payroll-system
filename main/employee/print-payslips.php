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

WHERE payroll.payroll_id=?
AND payroll.employee_id=?
");

$stmt->bind_param("ii",$payroll_id,$employee_id);
$stmt->execute();

$result = $stmt->get_result();

$payslip = $result->fetch_assoc();

$stmt->close();

if(!$payslip){
    header("Location:payslips.php");
    exit();
}
?>





<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Print Payslip</title>






<style>

body{

font-family:Arial,Helvetica,sans-serif;

margin:30px;

}

.company{

text-align:center;

margin-bottom:25px;

}

.company h2{

color:#1C5FA2;

margin-bottom:5px;

}

table{

width:100%;

border-collapse:collapse;

margin-bottom:25px;

}

table th,
table td{

border:1px solid #000;

padding:10px;

text-align:left;

}

th{

background:#f4f4f4;

}

.title{

background:#27348E;

color:white;

padding:10px;

margin-top:25px;

margin-bottom:10px;

}

.signature{

margin-top:70px;

display:flex;

justify-content:space-between;

}

@media print{

.no-print{

display:none;

}

body{

margin:10px;

}

}

</style>

</head>

<body>





<div class="company">

<img
src="../assets/images/logo.png"
width="90">

<h2>

MUDIAME UNIVERSITY IRRUA

</h2>

<p>

Employee Management System

</p>

<p>

Employee Payslip

</p>

</div>





<div class="title">

Employee Information

</div>

<table>

<tr>

<th width="35%">

Employee Number

</th>

<td>

<?= htmlspecialchars($payslip['employee_number']); ?>

</td>

</tr>

<tr>

<th>

Employee Name

</th>

<td>

<?= htmlspecialchars($payslip['first_name']." ".$payslip['last_name']); ?>

</td>

</tr>

<tr>

<th>

Department

</th>

<td>

<?= htmlspecialchars($payslip['department_name']); ?>

</td>

</tr>

<tr>

<th>

Position

</th>

<td>

<?= htmlspecialchars($payslip['position_name']); ?>

</td>

</tr>

<tr>

<th>

Payroll Month

</th>

<td>

<?= htmlspecialchars($payslip['payroll_month']); ?>

</td>

</tr>

</table>




<div class="title">

Earnings

</div>

<table>

<tr>

<th>

Basic Salary

</th>

<td>

₦<?= number_format($payslip['basic_salary'],2); ?>

</td>

</tr>

<tr>

<th>

Housing Allowance

</th>

<td>

₦<?= number_format($payslip['allowances'],2); ?>

</td>

</tr>




</table>





<div class="title">

Deductions

</div>

<table>

<tr>

<th>

Tax

</th>

<td>

₦<?= number_format($payslip['tax'],2); ?>

</td>

</tr>

<tr>

<th>

Pension

</th>

<td>

₦<?= number_format($payslip['pension'],2); ?>

</td>

</tr>

<tr>

<th>

Other Deductions

</th>

<td>

₦<?= number_format($payslip['deductions'],2); ?>

</td>

</tr>

<tr>

<th>

Net Salary

</th>

<td>

<strong>

₦<?= number_format($payslip['net_salary'],2); ?>

</strong>

</td>

</tr>

</table>





<div class="signature">

<div>

_____________________

<br>

Prepared By

</div>

<div>

_____________________

<br>

Authorized By

</div>

<div>

_____________________

<br>

Employee Signature

</div>

</div>




<div class="no-print" style="margin-top:40px;">

<button

onclick="window.print();"

class="btn btn-primary">

Print Payslip

</button>

<a

href="view-payslip.php?id=<?= $payslip['payroll_id']; ?>"

class="btn btn-secondary">

Back

</a>

</div>





<script>

window.onload=function(){

window.print();

}

</script>

</body>

</html>