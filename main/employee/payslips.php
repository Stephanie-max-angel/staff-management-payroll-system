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
?>






<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>My Payslips</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.css">

<style>

body{
    background:#F2F4F5;
}

.card-header{
    background:#27348E;
    color:white;
}

</style>

</head>
<body>

<div class="container mt-5">

<div class="card shadow">

<div class="card-header">

<h3>

My Payslips

</h3>

</div>

<div class="card-body">

<div class="table-responsive">
<table
id="payslipTable"
class="table table-bordered table-hover">

<thead class="table-primary">

<tr>

<th>Payroll Month</th>

<th>Basic Salary</th>

<th>Allowances</th>

<th>Deductions</th>

<th>Net Salary</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>


<?php

$stmt = $conn->prepare("
SELECT *
FROM payroll
WHERE employee_id=?
ORDER BY payroll_id DESC
");

$stmt->bind_param("i",$employee_id);

$stmt->execute();

$result = $stmt->get_result();

while($row = $result->fetch_assoc()){

?>

<tr>

<td>

<?= htmlspecialchars($row['payroll_month']); ?>

</td>

<td>

₦<?= number_format($row['basic_salary'],2); ?>

</td>

<td>

₦<?= number_format($row['allowances'],2); ?>

</td>

<td>

₦<?= number_format($row['deductions'],2); ?>

</td>

<td>

<strong>

₦<?= number_format($row['net_salary'],2); ?>

</strong>

</td>

<td>

<?php

if($row['payment_status']=="Paid"){

?>

<span class="badge bg-success">

Paid

</span>

<?php }else{ ?>

<span class="badge bg-warning">

Pending

</span>

<?php } ?>

</td>

<td>

<a

href="generate-payslip.php?id=<?= $row['payroll_id']; ?>"

class="btn btn-primary btn-sm">

View

</a>

</td>

</tr>

<?php

}

?>

</tbody>

</table>
</div>





</div>

</div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>

<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.js"></script>

<script>

new DataTable("#payslipTable");

</script>

</body>

</html>