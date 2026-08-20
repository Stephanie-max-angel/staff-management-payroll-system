<?php
session_start();
require_once("../admin/config/database.php");

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

$stmt->bind_param("i", $payroll_id);
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

<title>Print Payslip</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{

    background:white;

    font-family:Arial,sans-serif;

}

.payslip{

    width:900px;

    margin:auto;

    padding:40px;

}

.company{

    color:#1C5FA2;

    font-size:30px;

    font-weight:bold;

}

@media print{

.no-print{

display:none;

}

}

</style>

</head>

<body>







<div class="payslip">

<div class="text-center">

<h1 class="company">

MUDIAME UNIVERSITY IRRUA

</h1>

<h4>

EMPLOYEE PAYSLIP

</h4>

<hr>

</div>





<table class="table table-borderless">

<tr>

<td>

<strong>Employee Number:</strong>

<?= htmlspecialchars($payroll['employee_number']); ?>

</td>

<td>

<strong>Payroll:</strong>

<?= htmlspecialchars($payroll['payroll_month']); ?>

<?= htmlspecialchars($payroll['payroll_year']); ?>

</td>

</tr>

<tr>

<td>

<strong>Employee:</strong>

<?= htmlspecialchars($payroll['first_name']." ".$payroll['last_name']); ?>

</td>

<td>

<strong>Department:</strong>

<?= htmlspecialchars($payroll['department_name']); ?>

</td>

</tr>

<tr>

<td>

<strong>Position:</strong>

<?= htmlspecialchars($payroll['position_name']); ?>

</td>

<td>

<strong>Status:</strong>

<?= htmlspecialchars($payroll['payment_status']); ?>

</td>

</tr>

</table>






<table class="table table-bordered">

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

<td>Allowances</td>

<td class="text-end">

<?= number_format($payroll['allowances'],2); ?>

</td>

</tr>

<tr>

<td>Deductions</td>

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

<tr class="table-success">

<th>NET SALARY</th>

<th class="text-end">

₦<?= number_format($payroll['net_salary'],2); ?>

</th>

</tr>

</tbody>

</table>





<div class="row mt-5">

<div class="col-6 text-center">

________________________

<br>

Payroll Officer

</div>

<div class="col-6 text-center">

________________________

<br>

Employee Signature

</div>

</div>






<div class="text-center mt-5 no-print">

      <button 

      class="btn btn-primary"

      onclick="window.print();">

      Print Payslip

      </button>
      <a

      href="generate-payslip.php?id=<?= $payroll['payroll_id']; ?>"

      class="btn btn-success">

      Cancel

      </a>

</div>

</div>

</body>

</html>

