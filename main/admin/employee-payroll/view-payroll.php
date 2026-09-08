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

departments.department_name,

positions.position_name

FROM payroll

INNER JOIN employees

ON payroll.employee_id = employees.employee_id

INNER JOIN departments

ON employees.department_id = departments.department_id

INNER JOIN positions

ON employees.position_id = positions.position_id

WHERE payroll.payroll_id=?

");

$stmt->bind_param("i",$payroll_id);

$stmt->execute();

$result=$stmt->get_result();

$payroll=$result->fetch_assoc();

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

<title>Payroll Details</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">





<div class="container mt-5">

<div class="card shadow">

<div
class="card-header text-white"
style="background:#27348E;">

<h3>

Payroll Details

</h3>

</div>

<div class="card-body">




<table class="table table-bordered">

<tr>

<th width="30%">

Employee Number

</th>

<td>

<?= htmlspecialchars($payroll['employee_number']); ?>

</td>

</tr>

<tr>

<th>

Employee Name

</th>

<td>

<?= htmlspecialchars($payroll['first_name']." ".$payroll['last_name']); ?>

</td>

</tr>

<tr>

<th>

Department

</th>

<td>

<?= htmlspecialchars($payroll['department_name']); ?>

</td>

</tr>

<tr>

<th>

Position

</th>

<td>

<?= htmlspecialchars($payroll['position_name']); ?>

</td>

</tr>

<tr>

<th>

Payroll Month

</th>

<td>

<?= htmlspecialchars($payroll['payroll_month']); ?>

</td>

</tr>

<tr>

<th>

Payroll Year

</th>

<td>

<?= htmlspecialchars($payroll['payroll_year']); ?>

</td>

</tr>

</table>





<table class="table table-striped">

<thead class="table-primary">

<tr>

<th>

Salary Component

</th>

<th class="text-end">

Amount (₦)

</th>

</tr>

</thead>

<tbody>

<tr>

<td>

Basic Salary

</td>

<td class="text-end">

<?= number_format($payroll['basic_salary'],2); ?>

</td>

</tr>

<tr>

<td>

Allowances

</td>

<td class="text-end">

<?= number_format($payroll['allowances'],2); ?>

</td>

</tr>



<tr>

<td>

Pension

</td>

<td class="text-end">

<?= number_format((float)($payroll['pension'] ?? 0), 2) ?>

</td>

</tr>


<tr>

<td>

Other Deductions

</td>

<td class="text-end">

<?= number_format($payroll['deductions'],2); ?>

</td>

</tr>

<tr>

<td>

Tax

</td>

<td class="text-end">

<?= number_format($payroll['tax'],2); ?>

</td>

</tr>

<tr class="table-success">

<th>

Net Salary

</th>

<th class="text-end">

₦<?= number_format($payroll['net_salary'],2); ?>

</th>

</tr>

</tbody>

</table>





<div class="mt-3">

<strong>

Payment Status:

</strong>

<?php

if($payroll['payment_status']=="Paid"){

?>

<span class="badge bg-success">

Paid

</span>

<?php

}else{

?>

<span class="badge bg-warning">

Pending

</span>

<?php

}

?>

</div>





<div class="mt-4">

<a

href="edit-payroll.php?id=<?= $payroll['payroll_id']; ?>"

class="btn btn-warning">

Edit

</a>

<a

href="generate-payslip.php?id=<?= $payroll['payroll_id']; ?>"

class="btn btn-success">

Payslip

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

<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>